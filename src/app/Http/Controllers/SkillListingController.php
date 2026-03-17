<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillListingRequest;
use App\Models\SkillListing;
use App\Models\Skill;
use App\Services\SkillListingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $listings = $query->paginate(12);

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
        Log::info('[SkillListingController::store] スキル出品処理 開始');

        // 1) バリデーション済みデータを取得する
        // - validate() の中身は FormRequest（StoreSkillListingRequest）に集約する
        // - Controller は「受け取って、次の処理に渡す」に集中する
        $validated = $request->validated();

        Log::info('[SkillListingController::store] バリデーション済みデータ', [
            'title' => $validated['title'] ?? null,
            'price' => $validated['price'] ?? null,
            'delivery_days' => $validated['delivery_days'] ?? null,
            'keys' => array_keys($validated),
        ]);

        // 2) ログインしているフリーランスを取得する
        // - このルートは auth:freelancer + freelancer middleware の中にある想定
        // - なので Auth::guard('freelancer')->user() が取れるはず
        $user = Auth::guard('freelancer')->user();
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
}

