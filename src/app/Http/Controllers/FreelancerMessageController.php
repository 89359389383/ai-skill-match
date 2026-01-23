<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MessageRequest;
use App\Models\Application;
use App\Models\Message;
use App\Models\Scout;
use App\Models\Thread;
use App\Services\MessageService;

class FreelancerMessageController extends Controller
{
    /**
     * チャット画面（応募・スカウト共通）を表示し、未読を解除する
     */
    public function show(Thread $thread, MessageService $messageService)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // フリーランス以外は拒否する
        if ($user->role !== 'freelancer') {
            abort(403);
        }

        // freelancerプロフィールを取得する（当事者チェックに使用）
        $freelancer = $user->freelancer;

        // プロフィールが無い場合は先に登録へ誘導する
        if ($freelancer === null) {
            return redirect('/freelancer/profile')->with('error', '先にプロフィール登録が必要です');
        }

        // スレッドの当事者チェック（不正アクセス防止）
        if ((int) $thread->freelancer_id !== (int) $freelancer->id) {
            // 他人のスレッドは閲覧できない
            abort(403);
        }

        // 表示に必要な関連データを取得する（eager load）
        $thread->load([
            // チャット相手企業
            'company',
            // 案件（応募スレッドなら存在する）
            'job',
            // 会話履歴
            'messages',
        ]);

        // スレッド種別（スカウト/応募）を導出する
        $scout = null;
        $application = null;

        // job_id がnullならスカウトスレッド扱い
        if ($thread->job_id === null) {
            // Scout は company_id + freelancer_id + job_id(null) で導出する
            $scout = Scout::query()
                ->where('company_id', $thread->company_id)
                ->where('freelancer_id', $thread->freelancer_id)
                ->whereNull('job_id')
                ->latest('id')
                ->first();
        } else {
            // 応募は job_id + freelancer_id で導出する
            $application = Application::query()
                ->where('job_id', $thread->job_id)
                ->where('freelancer_id', $thread->freelancer_id)
                ->latest('id')
                ->first();

            // 応募が無い場合でも、案件紐付けスカウトの可能性があるため補助的に探す
            if ($application === null) {
                $scout = Scout::query()
                    ->where('company_id', $thread->company_id)
                    ->where('freelancer_id', $thread->freelancer_id)
                    ->where('job_id', $thread->job_id)
                    ->latest('id')
                    ->first();
            }
        }

        // thread を開いたタイミングで既読扱いにする
        $messageService->markRead($thread, 'freelancer');

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

        // スレッド種別に応じてビューを返す
        if ($scout !== null && $application === null) {
            return view('freelancer.scouts.show', [
                'thread' => $thread,
                // スカウト情報など
                'scout' => $scout,
                // 会話履歴
                'messages' => $thread->messages,
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

        // 応募チャットは応募専用ビュー
        return view('freelancer.messages.show', [
            'thread' => $thread,
            // 応募メッセージなど
            'application' => $application,
            // 会話履歴
            'messages' => $thread->messages,
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

    /**
     * メッセージを送信する（store は Service に委譲）
     */
    public function store(MessageRequest $request, Thread $thread, MessageService $messageService)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // フリーランス以外は拒否する
        if ($user->role !== 'freelancer') {
            abort(403);
        }

        // freelancerプロフィールを取得する
        $freelancer = $user->freelancer;

        // プロフィールが無い場合は先に登録へ誘導する
        if ($freelancer === null) {
            return redirect('/freelancer/profile')->with('error', '先にプロフィール登録が必要です');
        }

        // スレッドの当事者チェック
        if ((int) $thread->freelancer_id !== (int) $freelancer->id) {
            abort(403);
        }

        // 入力をバリデーションする（content required / FormRequest に委譲）
        $validated = $request->validated();

        // 送信処理を Service に委譲する（MessageService::send）
        $messageService->send($thread, 'freelancer', $freelancer->id, $validated['content']);

        // thread.show へ戻す
        return redirect()
            ->route('freelancer.threads.show', ['thread' => $thread])
            ->with('success', 'メッセージを送信しました');
    }

    /**
     * 直前の自分のメッセージを削除する（destroy は Service 不要）
     *
     * ※routes は /freelancer/messages/{message} なので thread なしで受け取る
     */
    public function destroy(Message $message)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // フリーランス以外は拒否する
        if ($user->role !== 'freelancer') {
            abort(403);
        }

        // freelancerプロフィールを取得する
        $freelancer = $user->freelancer;

        // プロフィールが無い場合は先に登録へ誘導する
        if ($freelancer === null) {
            return redirect('/freelancer/profile')->with('error', '先にプロフィール登録が必要です');
        }

        // message が属する thread を取得する（権限チェックに必要）
        $thread = $message->thread;

        // スレッドの当事者チェック（不正アクセス防止）
        if ($thread === null || (int) $thread->freelancer_id !== (int) $freelancer->id) {
            abort(403);
        }

        // 自分（freelancer）のメッセージかチェックする
        if ($message->sender_type !== 'freelancer' || (int) $message->sender_id !== (int) $freelancer->id) {
            abort(403);
        }

        // 「直前の自分のメッセージのみ」削除可を担保する
        $latestMessage = Message::query()
            ->where('thread_id', $thread->id)
            // 削除済みは除外して最新を取得する
            ->whereNull('deleted_at')
            ->orderByDesc('sent_at')
            ->first();

        // 最新メッセージがこのメッセージでなければ削除させない
        if ($latestMessage === null || (int) $latestMessage->id !== (int) $message->id) {
            return redirect()
                ->route('freelancer.threads.show', ['thread' => $thread])
                ->with('error', '削除できるのは直前の自分のメッセージのみです');
        }

        // 削除を実行する（SoftDeletes）
        $message->delete();

        // thread.show へ戻す
        return redirect()
            ->route('freelancer.threads.show', ['thread' => $thread])
            ->with('success', 'メッセージを削除しました');
    }
}