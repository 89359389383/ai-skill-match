<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\JobRequest;
use App\Models\Job;
use App\Models\Thread;
use App\Services\JobService;

class CompanyJobController extends Controller
{
    /**
     * 自社案件一覧を表示する（企業側）
     */
    public function index(Request $request)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 企業プロフィールを取得する（company_id の起点）
        $company = $user->company;

        // 企業プロフィール未登録なら先に登録へ誘導する
        if ($company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // フィルタ用のクエリを取得する（status/keyword は任意）
        $status = $request->query('status');
        $keyword = $request->query('keyword');
        // 報酬（万）の検索レンジ（GETクエリ）
        $rewardMinRaw = $request->query('reward_min');
        $rewardMaxRaw = $request->query('reward_max');

        // フォームからは「万」単位で受け取る -> サーバ側では円に変換して比較する
        $rewardMinWan = (is_numeric($rewardMinRaw) ? (int)$rewardMinRaw : null);
        $rewardMaxWan = (is_numeric($rewardMaxRaw) ? (int)$rewardMaxRaw : null);

        // 両方入っている時だけレンジとして扱う（サーバ側でも安全に）
        if ($rewardMinWan !== null && $rewardMaxWan !== null && $rewardMinWan > $rewardMaxWan) {
            // 下限/上限が逆なら入れ替える（ユーザー入力ミス耐性）
            [$rewardMinWan, $rewardMaxWan] = [$rewardMaxWan, $rewardMinWan];
        }

        // 万 -> 円 に変換（DBの単位が円のため）
        $rewardMin = ($rewardMinWan !== null ? $rewardMinWan * 10000 : null);
        $rewardMax = ($rewardMaxWan !== null ? $rewardMaxWan * 10000 : null);

        // 自社案件のみ対象にする
        $query = Job::query()->where('company_id', $company->id);

        // status フィルタがある場合に絞り込む
        if (is_string($status) && $status !== '') {
            // 画面仕様に合わせて文字列ステータスを数値ステータスへ変換する
            $statusMap = [
                'draft' => Job::STATUS_DRAFT,
                'publish' => Job::STATUS_PUBLISHED,
                'stopped' => Job::STATUS_STOPPED,
            ];

            // 既知の値だけを採用する
            if (array_key_exists($status, $statusMap)) {
                $query->where('status', $statusMap[$status]);
            }
        }

        // keyword がある場合、全体検索（LIKE連結）する
        if (is_string($keyword) && $keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                // タイトルにヒットさせる
                $q->where('title', 'like', '%' . $keyword . '%')
                    // 説明文にヒットさせる
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    // 報酬種別にヒットさせる
                    ->orWhere('reward_type', 'like', '%' . $keyword . '%')
                    // 必須スキルにヒットさせる
                    ->orWhere('required_skills_text', 'like', '%' . $keyword . '%')
                    // 稼働条件にヒットさせる
                    ->orWhere('work_time_text', 'like', '%' . $keyword . '%');
            });
        }

        // 報酬検索（端を含む「重なり」判定 / 片側入力にも対応）
        // - 条件（両方指定）: job_max >= rewardMin かつ job_min <= rewardMax
        // - 片側指定:
        //   - 下限のみ: job_max >= rewardMin
        //   - 上限のみ: job_min <= rewardMax
        $hasRewardFilter = ($rewardMin !== null || $rewardMax !== null);
        if ($hasRewardFilter) {
            if ($rewardMin !== null && $rewardMax !== null) {
                $query->where('max_rate', '>=', $rewardMin)
                    ->where('min_rate', '<=', $rewardMax);
            } elseif ($rewardMin !== null) {
                $query->where('max_rate', '>=', $rewardMin);
            } else { // $rewardMax !== null
                $query->where('min_rate', '<=', $rewardMax);
            }
        }

        // 自社案件一覧をページングして取得する
        $jobs = $query->orderByDesc('id')->paginate(20)->withQueryString();

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

        // 一覧ビューを返す
        return view('company.jobs.index', [
            // 表示用データ
            'jobs' => $jobs,
            // フィルタ保持用
            'status' => $status,
            // フィルタ保持用
            'keyword' => $keyword,
            // 報酬レンジを保持する
            'rewardMin' => $rewardMinRaw,
            'rewardMax' => $rewardMaxRaw,
            // 企業情報
            'company' => $company,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }

    /**
     * 案件作成フォームを表示する（企業側）
     */
    public function create()
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        $company = $user->company;

        // 企業プロフィール未登録なら先に登録へ誘導する
        if ($company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

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

        // フォームビューを返す
        return view('company.jobs.create', [
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }

    /**
     * 案件を登録する（store は Service に委譲）
     */
    public function store(JobRequest $request, JobService $jobService)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 企業プロフィールを取得する（company_id の起点）
        $company = $user->company;

        // 企業プロフィール未登録なら先に登録へ誘導する
        if ($company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // 入力をバリデーションする（FormRequest に委譲）
        $validated = $request->validated();

        // Service に保存処理を委譲する（JobService::store）
        $jobService->store($company->id, $validated);

        // 一覧へ戻して完了メッセージを表示する
        return redirect('/company/jobs')->with('success', '案件を登録しました');
    }

    /**
     * 案件編集フォームを表示する（企業側）
     */
    public function edit(Job $job)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 自社案件かどうかをチェックする（company_id一致）
        if ($user->company === null || $job->company_id !== $user->company->id) {
            // 自社以外の案件は編集できない
            abort(403);
        }

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

        // 編集ビューを返す
        return view('company.jobs.edit', [
            'job' => $job,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }

    /**
     * 案件を更新する（update は Service に委譲）
     */
    public function update(JobRequest $request, Job $job, JobService $jobService)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 自社案件かどうかをチェックする
        if ($user->company === null || $job->company_id !== $user->company->id) {
            abort(403);
        }

        // 入力をバリデーションする（FormRequest に委譲）
        $validated = $request->validated();

        // Service に更新処理を委譲する（JobService::update）
        $jobService->update($job, $validated);

        // 一覧へ戻して完了メッセージを表示する
        return redirect('/company/jobs')->with('success', '案件を更新しました');
    }

    /**
     * 案件のステータスを更新する
     */
    public function updateStatus(Request $request, Job $job)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 自社案件かどうかをチェックする
        if ($user->company === null || $job->company_id !== $user->company->id) {
            abort(403);
        }

        // ステータスをバリデーションする
        $request->validate([
            'status' => 'required|in:draft,publish,stopped',
        ]);

        // ステータスマップ
        $statusMap = [
            'draft' => Job::STATUS_DRAFT,
            'publish' => Job::STATUS_PUBLISHED,
            'stopped' => Job::STATUS_STOPPED,
        ];

        // ステータスを更新する
        $job->status = $statusMap[$request->status];
        $job->save();

        // 一覧へ戻して完了メッセージを表示する
        return redirect('/company/jobs')->with('success', 'ステータスを更新しました');
    }

    /**
     * 案件を削除する（destroy は単純削除のため Service 不要）
     */
    public function destroy(Job $job)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 自社案件かどうかをチェックする
        if ($user->company === null || $job->company_id !== $user->company->id) {
            abort(403);
        }

        // 案件を削除する（MVPでは物理削除）
        $job->delete();

        // 一覧へ戻して完了メッセージを表示する
        return redirect('/company/jobs')->with('success', '案件を削除しました');
    }
}