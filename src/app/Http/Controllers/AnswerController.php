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

        // 入力チェックは FormRequest 側へ移動（StoreAnswerRequest）
        $validated = $request->validated();

        $service->store($user, $question, $validated);

        return redirect()->route('questions.show', ['question' => $question->id]);
    }
}

