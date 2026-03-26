<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillListingRequest;
use App\Models\SkillListing;
use App\Models\Skill;
use App\Models\User;
use App\Services\SkillListingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SkillListingController extends Controller
{
    /**
     * スキル販売一覧（閲覧はログイン不要）。
     *
     * ここでやること:
     * - 公開中（status=1）の出品を取得
     * - 検索/絞り込みは将来対応（仕様書に合わせて拡張）
     */
    public function index(\Illuminate\Http\Request $request)
    {
        Log::info('[SkillListingController::index] スキル一覧表示 開始');

        // 公開中（status=1）の出品のみ一覧に表示
        $query = SkillListing::query()
            ->with(['freelancer.user', 'skills'])
            ->where('status', 1)
            ->orderByDesc('id');

        // 将来: query, price_min/max, category(skill) などをここに追加

        $listings = $query->paginate(30)->withQueryString();

        Log::info('[SkillListingController::index] スキル一覧取得完了', [
            'total' => $listings->total(),
            'current_page' => $listings->currentPage(),
            'per_page' => $listings->perPage(),
            'first_item' => $listings->firstItem(),
            'last_item' => $listings->lastItem(),
            'listings_count' => $listings->count(),
        ]);

        foreach ($listings as $i => $l) {
            Log::info("[SkillListingController::index] 一覧スキル #{$l->id}", [
                'index' => $i + 1,
                'id' => $l->id,
                'title' => $l->title,
                'status' => $l->status,
                'freelancer_id' => $l->freelancer_id,
                'price' => $l->price,
                'created_at' => $l->created_at?->toIso8601String(),
            ]);
        }

        Log::info('[SkillListingController::index] スキル一覧表示 終了');

        // View が無い間は welcome を返す（落とさないため）
        // 将来 view ができたら: return view('skills.index', compact('listings'));
        if (view()->exists('skills.index')) {
            return view('skills.index', compact('listings'));
        }

        return view('welcome');
    }

    /**
     * スキル販売詳細（閲覧はログイン不要）。
     */
    public function show(SkillListing $skill_listing)
    {
        // 画面で必要になる関係を一緒にロードしておく
        $skill_listing->load(['freelancer.user', 'skills', 'assets', 'reviews.user']);

        if (view()->exists('skills.show')) {
            return view('skills.show', ['listing' => $skill_listing]);
        }

        return view('welcome');
    }

    /**
     * スキル出品画面（フリーランスのみ・ログイン必須）。
     *
     * ここでやること:
     * - 共通スキル一覧を取得して、選択肢として渡す
     */
    public function create()
    {
        $skills = Skill::query()->orderBy('name')->get();

        if (view()->exists('skills.create')) {
            return view('skills.create', compact('skills'));
        }

        return view('welcome');
    }

    /**
     * スキル出品登録（フリーランスのみ・ログイン必須）。
     *
     * 注意:
     * - 「出品本体」「スキル紐付け」「添付」をまとめて保存する
     * - 途中で失敗すると不整合が出るため Service + transaction で安全に処理する
     */
    public function store(StoreSkillListingRequest $request, SkillListingService $service)
    {
        $freelancerUser = Auth::guard('freelancer')->user();

        Log::info('[SkillListingController::store] スキル出品処理 開始（CSRF・セッション通過後）', [
            'session_cookie_name' => config('session.cookie'),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'query_slot' => $request->query('slot'),
            'input_slot' => $request->input('slot'),
            'freelancer_guard_user_id' => $freelancerUser?->id,
            'route' => $request->route()?->getName(),
            'url' => $request->fullUrl(),
            'has_thumbnail_file' => $request->hasFile('thumbnail'),
            'thumbnail_client_mime' => $request->hasFile('thumbnail')
                ? $request->file('thumbnail')->getClientMimeType()
                : null,
            'thumbnail_size' => $request->hasFile('thumbnail')
                ? $request->file('thumbnail')->getSize()
                : null,
            'raw_skill_names_count' => is_array($request->input('skill_names'))
                ? count($request->input('skill_names'))
                : 0,
        ]);

        // 1) バリデーション済みデータを取得する
        // - validate() の中身は FormRequest（StoreSkillListingRequest）に集約する
        // - Controller は「受け取って、次の処理に渡す」に集中する
        Log::info('[SkillListingController::store] FormRequest 検証済み・validated() 取得直前');
        $validated = $request->validated();
        Log::info('[SkillListingController::store] validated() 取得完了', [
            'keys' => array_keys($validated),
        ]);

        // 画像アップロード（thumbnail）を thumbnail_url へ変換して保存用データに載せる
        $removeThumbnail = (bool) ($validated['remove_thumbnail'] ?? false);
        if ($removeThumbnail) {
            $validated['thumbnail_url'] = null;
        } elseif ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('skill_thumbnails', 'public');
            $validated['thumbnail_url'] = Storage::disk('public')->url($path);
        }

        // タグ（skill_names）を skills に確定し、skill_ids に変換して Service へ渡す
        if (array_key_exists('skill_names', $validated) && is_array($validated['skill_names'])) {
            $skillIds = [];
            foreach ($validated['skill_names'] as $name) {
                $name = trim((string) $name);
                if ($name === '') continue;
                $skill = Skill::firstOrCreate(['name' => $name]);
                $skillIds[] = $skill->id;
            }
            $skillIds = array_values(array_unique($skillIds));
            $validated['skill_ids'] = $skillIds;
        }

        Log::info('[SkillListingController::store] バリデーション済みデータ', [
            'title' => $validated['title'] ?? null,
            'price' => $validated['price'] ?? null,
            'delivery_days' => $validated['delivery_days'] ?? null,
            'keys' => array_keys($validated),
        ]);

        // 2) ログインしているフリーランスを取得する
        // - このルートは auth:freelancer + freelancer middleware の中にある想定
        // - なので Auth::guard('freelancer')->user() が取れるはず
        $user = $freelancerUser;
        $freelancer = $user?->freelancer;

        Log::info('[SkillListingController::store] フリーランス取得', [
            'user_id' => $user?->id,
            'freelancer_id' => $freelancer?->id,
            'freelancer_exists' => $freelancer !== null,
        ]);

        // もしここが null なら「プロフィール未作成」などの状態なので、丁寧に弾く
        if (!$freelancer) {
            Log::warning('[SkillListingController::store] フリーランス未作成のため出品不可');
            return back()->withErrors([
                'freelancer' => 'スキル出品の前に、フリーランスプロフィールを作成してください。',
            ]);
        }

        // 3) 保存（Service に委譲）
        Log::info('[SkillListingController::store] SkillListingService::store 呼び出し直前', [
            'freelancer_id' => $freelancer->id,
            'payload_skill_ids_count' => isset($validated['skill_ids']) && is_array($validated['skill_ids'])
                ? count($validated['skill_ids'])
                : 0,
            'thumbnail_url_set' => array_key_exists('thumbnail_url', $validated),
        ]);

        $listing = $service->store($freelancer, $validated);

        Log::info('[SkillListingController::store] スキル出品完了', [
            'listing_id' => $listing->id,
            'title' => $listing->title,
            'status' => $listing->status,
            'freelancer_id' => $listing->freelancer_id,
            'price' => $listing->price,
            'created_at' => $listing->created_at?->toIso8601String(),
            'redirect_to' => 'skills.index',
        ]);

        Log::info('[SkillListingController::store] スキル出品処理 終了');

        // 4) 完了後はスキル一覧へ遷移し、成功メッセージを表示
        return redirect()->route('skills.index')->with('success', 'スキルを出品しました');
    }

    /**
     * 指定フリーランスのスキル販売一覧（status=1のみ）。
     */
    public function skillsByFreelancer(User $user)
    {
        $freelancer = $user->freelancer()->first();
        if (!$freelancer) {
            abort(404, 'プロフィールが見つかりません。');
        }

        $listings = SkillListing::query()
            ->with(['freelancer.user', 'skills'])
            ->where('freelancer_id', $freelancer->id)
            ->where('status', 1)
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        if (view()->exists('skills.index')) {
            return view('skills.index', compact('listings', 'freelancer'));
        }

        return view('welcome');
    }

    /**
     * スキル出品編集画面（本人のみ）。
     */
    public function edit(SkillListing $skill_listing)
    {
        $user = Auth::guard('freelancer')->user();
        $freelancerId = $user?->freelancer?->id;

        if (!$freelancerId || (int) $skill_listing->freelancer_id !== (int) $freelancerId) {
            abort(403);
        }

        $skill_listing->load(['freelancer.user', 'skills', 'assets', 'reviews']);

        if (view()->exists('skills.edit')) {
            return view('skills.edit', ['listing' => $skill_listing]);
        }

        return view('welcome');
    }

    /**
     * スキル出品更新（本人のみ）。
     */
    public function update(StoreSkillListingRequest $request, SkillListing $skill_listing)
    {
        $user = Auth::guard('freelancer')->user();
        $freelancerId = $user?->freelancer?->id;

        if (!$freelancerId || (int) $skill_listing->freelancer_id !== (int) $freelancerId) {
            abort(403);
        }

        $validated = $request->validated();

        // 画像アップロード（thumbnail）/削除フラグを thumbnail_url へ変換
        $removeThumbnail = (bool) ($validated['remove_thumbnail'] ?? false);
        if ($removeThumbnail) {
            $validated['thumbnail_url'] = null;
        } elseif ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('skill_thumbnails', 'public');
            $validated['thumbnail_url'] = Storage::disk('public')->url($path);
        }

        // タグ（skill_names）→ skills を確定し skill_ids に変換
        if (array_key_exists('skill_names', $validated) && is_array($validated['skill_names'])) {
            $skillIds = [];
            foreach ($validated['skill_names'] as $name) {
                $name = trim((string) $name);
                if ($name === '') continue;
                $skill = Skill::firstOrCreate(['name' => $name]);
                $skillIds[] = $skill->id;
            }
            $skillIds = array_values(array_unique($skillIds));
            $validated['skill_ids'] = $skillIds;
        }

        $skill_listing->fill([
            'title' => $validated['title'] ?? $skill_listing->title,
            'description' => $validated['description'] ?? $skill_listing->description,
            'purchase_instructions' => $validated['purchase_instructions'] ?? $skill_listing->purchase_instructions,
            'price' => (int) ($validated['price'] ?? $skill_listing->price),
            'pricing_type' => $validated['pricing_type'] ?? $skill_listing->pricing_type,
            'thumbnail_url' => array_key_exists('thumbnail_url', $validated)
                ? $validated['thumbnail_url']
                : $skill_listing->thumbnail_url,
            // statusは更新ではそのまま（編集画面仕様に合わせて後で拡張）
            'delivery_days' => $validated['delivery_days'] ?? $skill_listing->delivery_days,
        ])->save();

        if (array_key_exists('skill_ids', $validated) && is_array($validated['skill_ids'])) {
            $skill_listing->skills()->sync($validated['skill_ids']);
        }

        // assets は現状 update 対応が簡易（thumbnail upload等が揃った後に拡張）
        if (array_key_exists('assets', $validated) && is_array($validated['assets'])) {
            // 簡易: assets を削除して再作成
            $skill_listing->assets()->delete();
            foreach ($validated['assets'] as $i => $asset) {
                if (!is_array($asset)) continue;
                $skill_listing->assets()->create([
                    'type' => $asset['type'] ?? 'image',
                    'url' => $asset['url'] ?? '',
                    'sort_order' => $asset['sort_order'] ?? $i,
                ]);
            }
        }

        $skill_listing->load('freelancer');

        return redirect()
            ->route('profiles.skills.index', ['user' => $skill_listing->freelancer->user_id])
            ->with('success', 'スキルを更新しました');
    }

    /**
     * スキル出品削除（本人のみ）。
     */
    public function destroy(SkillListing $skill_listing)
    {
        $user = Auth::guard('freelancer')->user();
        $freelancerId = $user?->freelancer?->id;

        if (!$freelancerId || (int) $skill_listing->freelancer_id !== (int) $freelancerId) {
            abort(403);
        }

        $freelancerUserId = $skill_listing->freelancer?->user_id;
        $skill_listing->delete();

        return redirect()
            ->route('profiles.skills.index', ['user' => $freelancerUserId])
            ->with('success', 'スキルを削除しました');
    }
}

