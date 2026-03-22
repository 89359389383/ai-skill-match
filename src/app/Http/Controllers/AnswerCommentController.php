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
