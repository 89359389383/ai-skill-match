<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnswerRequest;
use App\Models\Question;
use App\Services\AnswerService;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * 回答投稿（ログイン必須）。
     *
     * ここでやること:
     * - 権限チェック（質問者は回答不可、1ユーザー1回答制限）
     * - 回答本文の validate
     * - AnswerService に渡して保存
     * - 完了後、質問詳細へ戻す
     */
    public function store(StoreAnswerRequest $request, Question $question, AnswerService $service)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // 質問者は回答できない
        if ((int) $question->user_id === (int) $user->id) {
            return redirect()
                ->route('questions.show', ['question' => $question->id])
                ->with('error', '質問者自身は回答できません。');
        }

        // 1ユーザー1回答制限
        if ($service->hasAnswered($user, $question)) {
            return redirect()
                ->route('questions.show', ['question' => $question->id])
                ->with('error', 'この質問には既に回答済みです。回答は1つだけ投稿できます。');
        }

        // 入力チェックは FormRequest 側へ移動（StoreAnswerRequest）
        $validated = $request->validated();

        $service->store($user, $question, $validated);

        return redirect()
            ->route('questions.show', ['question' => $question->id])
            ->with('success', '回答を投稿しました。');
    }
}

