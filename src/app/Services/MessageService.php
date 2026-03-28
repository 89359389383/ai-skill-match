<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Thread;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MessageService
{
    /**
     * Thread を既読扱いにする（未読解除）
     *
     * 設計根拠（MessageService 詳細設計）
     * - チャット画面を開いたタイミングで未読フラグをOFFにする
     * - 未読判定は「最後の送信者が自分以外なら未読」を基本とする
     * - フェーズ1では既読テーブルは持たず、thread単位のフラグで管理する
     */
    public function markRead(Thread $thread, string $viewerType): void
    {
        // viewerType は 'company' または 'freelancer' を想定する（設計：当事者種別）
        if ($viewerType !== 'company' && $viewerType !== 'freelancer') {
            // 想定外の文字列はバグになりやすいので、ここで明示的に止める
            throw ValidationException::withMessages([
                'viewer_type' => 'viewerType は company または freelancer を指定してください',
            ]);
        }

        // 企業側が画面を開いた場合の未読解除
        if ($viewerType === 'company') {
            // 最後に送信したのが企業「以外」なら、企業側は未読扱いだったので解除できる
            if ($thread->latest_sender_type !== 'company') {
                // 企業側の未読フラグをOFFにする
                $thread->forceFill(['is_unread_for_company' => false])->save();
            }

            // 企業側の処理はここで終了する
            return;
        }

        // フリーランス側が画面を開いた場合の未読解除（企業側と同じ考え方）
        if ($thread->latest_sender_type !== 'freelancer') {
            // フリーランス側の未読フラグをOFFにする
            $thread->forceFill(['is_unread_for_freelancer' => false])->save();
        }
    }

    /**
     * メッセージを送信し、Thread の最新送信者・未読フラグを更新する
     *
     * 設計根拠:
     * - messages + threads を同時更新する（複数更新）
     * - 相手側を未読に切り替える（未読フラグ更新）
     */
    /**
     * メッセージを送信（本文＋添付（任意））
     *
     * @param array $attachments UploadedFile[]（任意）
     */
    public function send(Thread $thread, string $senderType, int $senderId, ?string $body, array $attachments = []): Message
    {
        // 入力の空白を取り除き、「空メッセージ」を弾きやすくする
        $body = trim((string) ($body ?? ''));

        // 本文も添付も無い場合はエラー
        $hasValidAttachments = false;
        foreach ($attachments as $file) {
            if ($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                $hasValidAttachments = true;
                break;
            }
        }
        if ($body === '' && !$hasValidAttachments) {
            throw ValidationException::withMessages([
                'content' => 'メッセージまたは添付ファイルを入力してください。',
            ]);
        }

        // senderType は 'company' または 'freelancer' を想定する
        if ($senderType !== 'company' && $senderType !== 'freelancer') {
            // 想定外の文字列は不正なので止める
            throw ValidationException::withMessages([
                'sender_type' => 'senderType は company または freelancer を指定してください',
            ]);
        }

        // 送信者がスレッド当事者かを確認する（設計：当事者チェック）
        if ($senderType === 'company' && (int) $senderId !== (int) $thread->company_id) {
            // companyはthread.company_idと一致している必要がある
            throw ValidationException::withMessages([
                'sender_id' => '送信者がこのスレッドの企業と一致しません',
            ]);
        }

        // 送信者がスレッド当事者かを確認する（フリーランス側）
        if ($senderType === 'freelancer' && (int) $senderId !== (int) $thread->freelancer_id) {
            // freelancerはthread.freelancer_idと一致している必要がある
            throw ValidationException::withMessages([
                'sender_id' => '送信者がこのスレッドのフリーランスと一致しません',
            ]);
        }

        // 複数更新が絡むのでトランザクションで安全にまとめる
        return DB::transaction(function () use ($thread, $senderType, $senderId, $body, $attachments): Message {
            // 時刻を1回だけ作って、thread/messageの整合を取りやすくする
            $now = Carbon::now();

            // messages にメッセージを保存する（設計：messagesテーブル更新）
            $message = Message::create([
                // どのスレッドのメッセージか
                'thread_id' => $thread->id,
                // 送信者の種別
                'sender_type' => $senderType,
                // 送信者ID（company_id or freelancer_id）
                'sender_id' => $senderId,
                // メッセージ本文
                'body' => $body,
                // 送信時刻
                'sent_at' => $now,
            ]);

            // 添付保存（任意）
            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    if (!$file || !is_object($file) || !method_exists($file, 'isValid') || !$file->isValid()) continue;

                    $clientName = $file->getClientOriginalName();
                    $mime = $file->getClientMimeType();
                    $sizeBytes = (int) ($file->getSize() ?? 0);
                    $storedPath = $file->store('thread-message-attachments', 'public');

                    $message->attachments()->create([
                        'attachment_name' => $clientName,
                        'attachment_path' => $storedPath,
                        'attachment_mime' => $mime,
                        'attachment_size' => $sizeBytes,
                    ]);
                }
            }

            // 未読フラグを「送信者に基づいて」更新する（設計：最後の送信者が自分以外なら未読）
            $isUnreadForCompany = $senderType !== 'company';
            // フリーランス側は「送信者がフリーランス以外なら未読」
            $isUnreadForFreelancer = $senderType !== 'freelancer';

            // threads 側の最新情報（last_sender/last_message_at）を更新する（設計：threadsテーブル更新）
            $thread->forceFill([
                // 最後に送った側
                'latest_sender_type' => $senderType,
                // 最後に送った人（company_id or freelancer_id）
                'latest_sender_id' => $senderId,
                // 最終送信時刻
                'latest_message_at' => $now,
                // 企業側の未読フラグ（相手が送ったらtrue）
                'is_unread_for_company' => $isUnreadForCompany,
                // フリーランス側の未読フラグ（相手が送ったらtrue）
                'is_unread_for_freelancer' => $isUnreadForFreelancer,
            ])->save();

            // 作成したメッセージを返す（Controllerは基本的にredirectする）
            return $message;
        });
    }
}

