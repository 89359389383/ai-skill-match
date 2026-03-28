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

class CompanyMessageController extends Controller
{
    /**
     * チャット画面（応募・スカウト共通）を表示し、未読を解除する
     */
    public function show(Thread $thread, MessageService $messageService)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // companyプロフィールを取得する（当事者チェックに使用）
        $company = $user->company;

        // companyプロフィールが無い場合は先に登録へ誘導する
        if ($company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // スレッドの当事者チェック（不正アクセス防止）
        if ((int) $thread->company_id !== (int) $company->id) {
            // 他社のスレッドは閲覧できない
            abort(403);
        }

        // 表示に必要な関連データを取得する（eager load）
        $thread->load([
            // チャット相手フリーランス
            'freelancer',
            // 案件（応募スレッドなら存在する）
            'job',
            // 企業情報
            'company',
            // 会話履歴
            'messages.attachments',
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
        $messageService->markRead($thread, 'company');

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

        // スレッド種別に応じてビューを返す
        if ($scout !== null && $application === null) {
            // スカウトチャットの場合はスカウト用ビュー
            return view('company.scouts.show', [
                // thread 本体
                'thread' => $thread,
                // スカウト情報など
                'scout' => $scout,
                // 会話履歴
                'messages' => $thread->messages,
                // ヘッダー用未読数
                'unreadApplicationCount' => $unreadApplicationCount,
                'unreadScoutCount' => $unreadScoutCount,
                // ユーザー情報
                'userInitial' => $userInitial,
            ]);
        }

        // 応募チャットの場合は応募用ビュー
        return view('company.messages.show', [
            // thread 本体
            'thread' => $thread,
            // 応募情報（ステータス表示などに使用）
            'application' => $application,
            // 会話履歴
            'messages' => $thread->messages,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }

    /**
     * メッセージを送信する（store は Service に委譲）
     */
    public function store(MessageRequest $request, Thread $thread, MessageService $messageService)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // companyプロフィールを取得する
        $company = $user->company;

        // companyプロフィールが無い場合は先に登録へ誘導する
        if ($company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // スレッドの当事者チェック
        if ((int) $thread->company_id !== (int) $company->id) {
            abort(403);
        }

        // 入力をバリデーションする（content required / FormRequest に委譲）
        $validated = $request->validated();

        // 添付がある場合は同時に保存できるように渡す
        $attachments = $request->file('attachments', []);

        // 送信処理を Service に委譲する（MessageService::send）
        $messageService->send(
            $thread,
            'company',
            $company->id,
            $validated['content'] ?? null,
            is_array($attachments) ? $attachments : []
        );

        // thread.show へ戻す
        return redirect()
            ->route('company.threads.show', ['thread' => $thread])
            ->with('success', 'メッセージを送信しました');
    }

    /**
     * 直前の自分のメッセージを削除する（destroy は Service 不要）
     *
     * ※routes は /company/messages/{message} なので thread なしで受け取る
     */
    public function destroy(Message $message)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // companyプロフィールを取得する
        $company = $user->company;

        // companyプロフィールが無い場合は先に登録へ誘導する
        if ($company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // message が属する thread を取得する（権限チェックに必要）
        $thread = $message->thread;

        // スレッドの当事者チェック
        if ($thread === null || (int) $thread->company_id !== (int) $company->id) {
            abort(403);
        }

        // 自分（company）のメッセージかチェックする
        if ($message->sender_type !== 'company' || (int) $message->sender_id !== (int) $company->id) {
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
                ->route('company.threads.show', ['thread' => $thread])
                ->with('error', '削除できるのは直前の自分のメッセージのみです');
        }

        // 削除を実行する（SoftDeletes）
        $message->delete();

        // thread.show へ戻す
        return redirect()
            ->route('company.threads.show', ['thread' => $thread])
            ->with('success', 'メッセージを削除しました');
    }

    /**
     * 応募ステータスを更新する
     */
    public function updateApplicationStatus(Request $request, Thread $thread)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // companyプロフィールを取得する
        $company = $user->company;

        // companyプロフィールが無い場合は先に登録へ誘導する
        if ($company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // スレッドの当事者チェック
        if ((int) $thread->company_id !== (int) $company->id) {
            abort(403);
        }

        // 応募スレッドでない場合は拒否（job_idがnullの場合はスカウトスレッド）
        if ($thread->job_id === null) {
            abort(403);
        }

        // ステータスをバリデーションする
        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        // 応募を取得する
        $application = Application::query()
            ->where('job_id', $thread->job_id)
            ->where('freelancer_id', $thread->freelancer_id)
            ->first();

        // 応募が存在しない場合はエラー
        if ($application === null) {
            return redirect()
                ->route('company.threads.show', ['thread' => $thread])
                ->with('error', '応募情報が見つかりません');
        }

        // ステータスを更新する
        $application->status = (int) $request->status;
        $application->save();

        // thread.show へ戻す
        return redirect()
            ->route('company.threads.show', ['thread' => $thread])
            ->with('success', 'ステータスを更新しました');
    }
}