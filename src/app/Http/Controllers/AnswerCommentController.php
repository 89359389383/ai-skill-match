<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerComment;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AnswerCommentController extends Controller
{
    /**
     * コメントを投稿する
     *
     * @param Request $request
     * @param Question $question
     * @param Answer $answer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Question $question, Answer $answer)
    {
        // 認証チェック
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('auth.login.form')
                ->with('error', 'コメントを投稿するにはログインが必要です。');
        }

        // 質問と回答の関連チェック
        if ($answer->question_id !== $question->id) {
            return redirect()->route('questions.show', $question)
                ->with('error', '不正なアクセスです。');
        }

        // コメント権限チェック（質問者 or 回答者のみ）
        $isQuestioner = $question->user_id === $user->id;
        $isAnswerer = $answer->user_id === $user->id;

        if (!$isQuestioner && !$isAnswerer) {
            return redirect()->route('questions.show', $question)
                ->with('error', 'この回答にコメントする権限がありません。');
        }

        // 解決済み（ベストアンサー確定後）のコメント制御
        // - ベストアンサーへ: 質問者は1回だけコメント可能
        // - 上記以外: 質問者/回答者ともコメント不可
        $isResolved = $question->status === Question::STATUS_RESOLVED || $question->best_answer_id !== null;
        if ($isResolved) {
            $isBestAnswer = $question->best_answer_id !== null && (int) $answer->id === (int) $question->best_answer_id;

            // ベストアンサーでなければコメント不可
            if (! $isBestAnswer) {
                return redirect()->route('questions.show', $question)
                    ->with('error', 'この質問は解決済みのためコメントできません。');
            }

            // 質問者以外はコメント不可
            if (! $isQuestioner) {
                return redirect()->route('questions.show', $question)
                    ->with('error', 'ベストアンサー確定後は質問者のみコメントできます。');
            }

            // 質問者は同一回答（ベストアンサー）へ1回のみ
            $alreadyCommented = AnswerComment::query()
                ->where('answer_id', $answer->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($alreadyCommented) {
                return redirect()->route('questions.show', $question)
                    ->with('error', 'ベストアンサー確定後のコメントは1回のみ可能です。');
            }
        } else {
            // 未解決時：
            // - 質問者は、直前のコメントが質問者本人ではないときだけコメント可能
            // - 回答者は、直前のコメントが質問者のときだけコメント可能
            $lastComment = AnswerComment::query()
                ->where('answer_id', $answer->id)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first();

            $lastCommentUserId = $lastComment?->user_id;

            if ($isQuestioner) {
                // 直前が質問者本人なら連投NG
                if ($lastCommentUserId !== null && (int) $lastCommentUserId === (int) $question->user_id) {
                    return redirect()->route('questions.show', $question)
                        ->with('error', '相手からの返信があるまで、コメントは送信できません。');
                }
            }

            if ($isAnswerer) {
                // 直前が質問者でないならNG（= 相手からの返信がない）
                if ($lastCommentUserId === null || (int) $lastCommentUserId !== (int) $question->user_id) {
                    return redirect()->route('questions.show', $question)
                        ->with('error', '質問者からのコメントがあるまで、コメントは送信できません。');
                }
            }
        }

        // バリデーション
        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'max:2000'],
        ], [
            'content.required' => 'コメント内容を入力してください。',
            'content.string' => 'コメント内容は文字列で入力してください。',
            'content.max' => 'コメント内容は2000文字以内で入力してください。',
        ]);

        if ($validator->fails()) {
            return redirect()->route('questions.show', ['question' => $question, 'focus_comment' => $answer->id])
                ->withErrors($validator)
                ->withInput();
        }

        // コメント作成
        AnswerComment::create([
            'answer_id' => $answer->id,
            'user_id' => $user->id,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('questions.show', ['question' => $question, 'focus_answer' => $answer->id])
            ->with('success', 'コメントを投稿しました。');
    }
}
