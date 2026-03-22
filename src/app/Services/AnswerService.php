<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnswerService
{
    /**
     * 回答を新規投稿する。
     *
     * - answers を作る
     * - questions.answers_count を増やす
     *
     * の2つを同じ結果になるようにまとめて処理する。
     *
     * @throws \InvalidArgumentException 既に回答済みの場合
     */
    public function store(User $user, Question $question, array $data): Answer
    {
        return DB::transaction(function () use ($user, $question, $data): Answer {
            $answer = Answer::create([
                'question_id' => $question->id,
                'user_id' => $user->id,
                'content' => $data['content'],
            ]);

            // 集計カラムを更新（一覧/詳細で高速に表示するため）
            $question->increment('answers_count');

            return $answer;
        });
    }

    /**
     * ユーザーが質問に既に回答しているかチェック
     */
    public function hasAnswered(User $user, Question $question): bool
    {
        return Answer::where('question_id', $question->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
