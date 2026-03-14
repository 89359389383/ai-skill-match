<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuestionService
{
    /**
     * 質問を新規作成する。
     *
     * - 質問本体（questions）
     * - タグ紐付け（question_tag）
     *
     * を一括で扱うため、transaction でまとめる。
     */
    public function store(User $user, array $data): Question
    {
        return DB::transaction(function () use ($user, $data): Question {
            $question = Question::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'content' => $data['content'],
                'category' => $data['category'] ?? 'すべて',
                'is_resolved' => false,
            ]);

            $tagNames = $data['tags'] ?? [];
            $tagIds = $this->resolveTagIds($tagNames);
            if (count($tagIds) > 0) {
                $question->tags()->sync($tagIds);
            }

            return $question;
        });
    }

    private function resolveTagIds(array $tagNames): array
    {
        $tagIds = [];

        foreach ($tagNames as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $tag = Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        return array_values(array_unique($tagIds));
    }
}

