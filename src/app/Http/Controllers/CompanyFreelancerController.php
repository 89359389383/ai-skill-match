<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Freelancer;
use App\Models\Thread;

class CompanyFreelancerController extends Controller
{
    /**
     * フリーランス一覧（検索付き）を表示する
     */
    public function index(Request $request)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 企業プロフィールが無い場合は先に登録へ誘導する
        if ($user->company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // 検索キーワードを取得する（keywordはGETクエリ）
        $keyword = $request->query('keyword');
        // 希望単価（万円）の検索レンジ（GETクエリ）
        $rateMinRaw = $request->query('rate_min');
        $rateMaxRaw = $request->query('rate_max');

        $rateMin = (is_numeric($rateMinRaw) ? (int)$rateMinRaw : null);
        $rateMax = (is_numeric($rateMaxRaw) ? (int)$rateMaxRaw : null);

        // 両方入っている時だけレンジとして扱う（HTML側でrequiredにしているが、サーバ側でも安全に）
        if ($rateMin !== null && $rateMax !== null && $rateMin > $rateMax) {
            // 下限/上限が逆なら入れ替える（ユーザー入力ミス耐性）
            [$rateMin, $rateMax] = [$rateMax, $rateMin];
        }

        // フリーランスをベースに検索クエリを作る（プロフィール全体を検索）
        $query = Freelancer::query()->with(['skills', 'customSkills', 'portfolios']);

        // keyword がある場合、LIKE検索をかける（横断検索）
        if (is_string($keyword) && $keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                // 表示名にヒットさせる
                $q->where('display_name', 'like', '%' . $keyword . '%')
                    // 職種にヒットさせる
                    ->orWhere('job_title', 'like', '%' . $keyword . '%')
                    // 自己紹介にヒットさせる
                    ->orWhere('bio', 'like', '%' . $keyword . '%')
                    // 働き方にヒットさせる
                    ->orWhere('work_style_text', 'like', '%' . $keyword . '%')
                    // 経験企業にヒットさせる
                    ->orWhere('experience_companies', 'like', '%' . $keyword . '%')
                    // 希望単価（数値）にもヒットさせる
                    ->orWhereRaw('CAST(min_rate as CHAR) like ?', ['%' . $keyword . '%'])
                    ->orWhereRaw('CAST(max_rate as CHAR) like ?', ['%' . $keyword . '%'])
                    // マスタスキル名にもヒットさせる（JOIN代替: whereHas）
                    ->orWhereHas('skills', function ($sq) use ($keyword) {
                        $sq->where('name', 'like', '%' . $keyword . '%');
                    })
                    // カスタムスキル名にもヒットさせる
                    ->orWhereHas('customSkills', function ($cq) use ($keyword) {
                        $cq->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        // 希望単価検索（端を含む「重なり」判定 / 片側入力にも対応）
        // - 例: 20〜30万の人は、30〜40 / 10〜20 でも「30」「20」で一致として表示する（端一致OK）
        // - 条件（両方指定）: freelancer_max >= rateMin かつ freelancer_min <= rateMax
        // - 片側指定:
        //   - 下限のみ: freelancer_max >= rateMin
        //   - 上限のみ: freelancer_min <= rateMax
        // - 検索が行われた場合、希望単価が未設定（min/maxとも0/NULL）のプロフィールは除外する
        $hasRateFilter = ($rateMin !== null || $rateMax !== null);
        if ($hasRateFilter) {
            // 未設定（両方0/NULL）を除外
            $query->where(function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNotNull('min_rate')->where('min_rate', '>', 0);
                })->orWhere(function ($qq) {
                    $qq->whereNotNull('max_rate')->where('max_rate', '>', 0);
                });
            });

            if ($rateMin !== null && $rateMax !== null) {
                $query->whereRaw('COALESCE(NULLIF(max_rate, 0), NULLIF(min_rate, 0)) >= ?', [$rateMin])
                    ->whereRaw('COALESCE(NULLIF(min_rate, 0), NULLIF(max_rate, 0)) <= ?', [$rateMax]);
            } elseif ($rateMin !== null) {
                $query->whereRaw('COALESCE(NULLIF(max_rate, 0), NULLIF(min_rate, 0)) >= ?', [$rateMin]);
            } else { // $rateMax !== null
                $query->whereRaw('COALESCE(NULLIF(min_rate, 0), NULLIF(max_rate, 0)) <= ?', [$rateMax]);
            }
        }

        // 一覧はページングで取得する
        $freelancers = $query->orderByDesc('id')->paginate(20)->withQueryString();

        // 企業IDを取得
        $companyId = $user->company->id;

        // 各フリーランスに対してスカウト済みかどうか（スレッドが存在するか）を確認
        $scoutThreadMap = [];
        foreach ($freelancers as $freelancer) {
            // スカウトスレッド（job_idがnull）を探す
            $thread = Thread::where('company_id', $companyId)
                ->where('freelancer_id', $freelancer->id)
                ->whereNull('job_id')
                ->first();
            
            if ($thread) {
                $scoutThreadMap[$freelancer->id] = $thread->id;
            }
        }

        // 応募に関連するthreadの未読数（企業側）
        $unreadApplicationCount = Thread::query()
            ->where('company_id', $companyId)
            ->whereNotNull('job_id') // 応募はjob_idが必須
            ->where('is_unread_for_company', true)
            ->count();

        // スカウトに関連するthreadの未読数（企業側、job_idがnullのもの）
        $unreadScoutCount = Thread::query()
            ->where('company_id', $companyId)
            ->whereNull('job_id') // スカウトはjob_idがnull
            ->where('is_unread_for_company', true)
            ->count();

        // ユーザー名の最初の文字を取得（アバター表示用）
        $userInitial = '企';
        if ($user->company !== null && !empty($user->company->name)) {
            $userInitial = mb_substr($user->company->name, 0, 1);
        } elseif (!empty($user->email)) {
            $userInitial = mb_substr($user->email, 0, 1);
        }

        // 一覧ビューへ返す
        return view('company.freelancers.index', [
            // 表示用の一覧
            'freelancers' => $freelancers,
            // 検索キーワードを保持する
            'keyword' => $keyword,
            // 希望単価レンジを保持する
            'rateMin' => $rateMinRaw,
            'rateMax' => $rateMaxRaw,
            // スカウト済みフリーランスのスレッドIDマップ
            'scoutThreadMap' => $scoutThreadMap,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }

    /**
     * フリーランス詳細を表示する
     */
    public function show(Freelancer $freelancer)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 企業プロフィールが無い場合は先に登録へ誘導する
        if ($user->company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // 表示用に必要なリレーションを読み込む
        $freelancer->load(['skills', 'customSkills', 'portfolios']);

        $company = $user->company;

        // 応募に関連するthreadの未読数（企業側）
        $unreadApplicationCount = Thread::query()
            ->where('company_id', $company->id)
            ->whereNotNull('job_id') // 応募はjob_idが必須
            ->where('is_unread_for_company', true)
            ->count();

        // スカウトに関連するthreadの未読数（企業側、job_idがnullのもの）
        $unreadScoutCount = Thread::query()
            ->where('company_id', $company->id)
            ->whereNull('job_id') // スカウトはjob_idがnull
            ->where('is_unread_for_company', true)
            ->count();

        // ユーザー名の最初の文字を取得（アバター表示用）
        $userInitial = '企';
        if ($company !== null && !empty($company->name)) {
            $userInitial = mb_substr($company->name, 0, 1);
        } elseif (!empty($user->email)) {
            $userInitial = mb_substr($user->email, 0, 1);
        }

        // 詳細ビューへ返す
        return view('company.freelancers.show', [
            // 表示対象のフリーランス
            'freelancer' => $freelancer,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }
}