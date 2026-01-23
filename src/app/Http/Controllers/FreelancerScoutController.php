<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\Scout;
use App\Models\Thread;

class FreelancerScoutController extends Controller
{
    /**
     * スカウト一覧を表示する（フリーランス側）
     *
     * - スカウトは job_id = null の thread を対象
     * - 未読はスレッド単位（thread.is_unread_for_freelancer）
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'freelancer') {
            abort(403);
        }

        $freelancer = $user->freelancer;

        if ($freelancer === null) {
            return redirect('/freelancer/profile')->with('error', '先にプロフィール登録が必要です');
        }

        $threads = Thread::query()
            ->where('freelancer_id', $freelancer->id)
            ->whereNull('job_id')
            ->with([
                'company',
                'messages' => function ($q) {
                    $q->whereNull('deleted_at')
                        ->orderByDesc('sent_at')
                        ->limit(1);
                },
            ])
            ->orderByDesc('latest_message_at')
            ->paginate(20)
            ->withQueryString();

        // 未読判定とスカウト情報を付ける
        $threads->getCollection()->transform(function (Thread $thread) use ($freelancer) {
            $thread->is_unread = (bool) $thread->is_unread_for_freelancer;

            // スカウト情報を取得（job_idがnullのスカウト）
            $scout = Scout::query()
                ->where('company_id', $thread->company_id)
                ->where('freelancer_id', $freelancer->id)
                ->whereNull('job_id')
                ->latest('id')
                ->first();
            
            $thread->scout = $scout;

            return $thread;
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

        return view('freelancer.scouts.index', [
            'threads' => $threads,
            'applicationCount' => $applicationCount,
            'scoutCount' => $scoutCount,
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            'userInitial' => $userInitial,
            'freelancer' => $freelancer,
        ]);
    }
}