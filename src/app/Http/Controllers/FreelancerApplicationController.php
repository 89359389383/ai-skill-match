<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\Scout;
use App\Models\Thread;

class FreelancerApplicationController extends Controller
{
    /**
     * 応募済み案件一覧（フリーランス側）を表示する
     *
     * 入口:
     * - GET /freelancer/applications?status=pending
     * - GET /freelancer/applications?status=closed
     */
    public function index(Request $request)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // フリーランス以外は拒否する
        if ($user->role !== 'freelancer') {
            abort(403);
        }

        // freelancerプロフィールを取得する（自分の応募だけを見るため）
        $freelancer = $user->freelancer;

        // プロフィールが無ければ先に登録へ誘導する
        if ($freelancer === null) {
            return redirect('/freelancer/profile')->with('error', '先にプロフィール登録が必要です');
        }

        // status パラメータを取得する
        $status = $request->query('status', 'pending');

        // 自分の応募だけを取得する
        $query = Application::query()
            ->where('freelancer_id', $freelancer->id)
            // 表示に必要なリレーションを先読みする
            ->with(['job.company']);

        // status に応じて絞り込む
        if ($status === 'closed') {
            // closed は クローズ のみ
            $query->where('status', Application::STATUS_CLOSED);
        } else {
            // pending は 未対応/対応中
            $query->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_IN_PROGRESS]);
        }

        // 応募一覧を取得する（最新順）
        $applications = $query->orderByDesc('id')->paginate(20)->withQueryString();

        // 応募に対応する thread をまとめて取得する（未読判定用）
        $threadsByKey = Thread::query()
            ->where('freelancer_id', $freelancer->id)
            ->whereIn('job_id', $applications->getCollection()->pluck('job_id')->unique()->values())
            ->get()
            ->keyBy(function (Thread $t) {
                // 本来 thread は job_id + freelancer_id で一意だが、ここでは freelancer は固定のため job_id で十分
                return (string) $t->job_id;
            });

        // 応募に thread と未読フラグを付与する（未読=相手が最新送信者）
        $applications->getCollection()->transform(function (Application $app) use ($threadsByKey) {
            // 応募の job_id から thread を導出する
            $thread = $threadsByKey->get((string) $app->job_id);

            // viewで使えるように thread を付与する
            $app->thread = $thread;

            // 未読判定（相手が最新送信者なら未読）
            $app->is_unread = $thread ? ($thread->latest_sender_type !== 'freelancer') : false;

            // threadのフラグがあればそちらを優先する
            if ($thread) {
                $app->is_unread = (bool) $thread->is_unread_for_freelancer;
            }

            // 付与した応募を返す
            return $app;
        });

        // ヘッダー用の応募数とスカウト数を取得
        $applicationCount = Application::query()
            ->where('freelancer_id', $freelancer->id)
            ->count();
        $scoutCount = Scout::query()
            ->where('freelancer_id', $freelancer->id)
            ->count();

        // 未読スカウト数を取得（ヘッダー用）
        $unreadScoutCount = Thread::query()
            ->where('freelancer_id', $freelancer->id)
            ->whereNull('job_id')
            ->where('is_unread_for_freelancer', true)
            ->count();

        // 未読応募数を取得（ヘッダー用）
        $unreadApplicationCount = Thread::query()
            ->where('freelancer_id', $freelancer->id)
            ->whereNotNull('job_id')
            ->where('is_unread_for_freelancer', true)
            ->count();

        // ユーザー名の最初の文字を取得（アバター表示用）
        $userInitial = 'U';
        if ($freelancer !== null && !empty($freelancer->display_name)) {
            $userInitial = mb_substr($freelancer->display_name, 0, 1);
        } elseif (!empty($user->email)) {
            $userInitial = mb_substr($user->email, 0, 1);
        }

        // 一覧ビューへ返す
        return view('freelancer.applications.index', [
            // 表示用応募一覧
            'applications' => $applications,
            // タブ制御用
            'status' => $status,
            // ヘッダー用の応募数
            'applicationCount' => $applicationCount,
            // ヘッダー用のスカウト数
            'scoutCount' => $scoutCount,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー名の最初の文字
            'userInitial' => $userInitial,
            // フリーランス情報（アイコン表示用）
            'freelancer' => $freelancer,
        ]);
    }
}