<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillListingRequest;
use App\Models\SkillListing;
use App\Models\Skill;
use App\Services\SkillListingService;
use Illuminate\Support\Facades\Auth;

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
        // まずは「一覧に出す最低限の情報」を取る
        $query = SkillListing::query()
            ->with(['freelancer'])
            ->orderByDesc('id');

        // 将来: query, price_min/max, category(skill) などをここに追加

        $listings = $query->paginate(12);

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
        // 1) バリデーション済みデータを取得する
        // - validate() の中身は FormRequest（StoreSkillListingRequest）に集約する
        // - Controller は「受け取って、次の処理に渡す」に集中する
        $validated = $request->validated();

        // 2) ログインしているフリーランスを取得する
        // - このルートは auth:freelancer + freelancer middleware の中にある想定
        // - なので Auth::guard('freelancer')->user() が取れるはず
        $user = Auth::guard('freelancer')->user();
        $freelancer = $user?->freelancer;

        // もしここが null なら「プロフィール未作成」などの状態なので、丁寧に弾く
        if (!$freelancer) {
            return back()->withErrors([
                'freelancer' => 'スキル出品の前に、フリーランスプロフィールを作成してください。',
            ]);
        }

        // 3) 保存（Service に委譲）
        $listing = $service->store($freelancer, $validated);

        // 4) 完了後は詳細へ（本来はフラッシュメッセージも）
        return redirect()->route('skills.show', ['skill_listing' => $listing->id]);
    }
}

