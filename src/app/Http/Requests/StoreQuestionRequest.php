<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルート側で auth.any をかけている前提。
        return true;
    }

    /**
     * 入力前処理：
     * - tags が存在する場合、個々の要素を文字列に変換して空要素を除去する。
     *   API 経由で数値などが来ても文字列化してバリデーションに通るようにする。
     */
    protected function prepareForValidation(): void
    {
        $tags = $this->input('tags', null);
        if (is_array($tags)) {
            $normalized = array_values(array_filter(array_map(function ($t) {
                if (is_null($t)) return null;
                return (string) $t;
            }, $tags), function ($v) {
                return trim($v) !== '';
            }));
            $this->merge(['tags' => $normalized]);
        }
    }

    public function rules(): array
    {
        return [
            // タイトル（必須）
            'title' => ['required', 'string', 'max:255'],

            // 本文（必須）
            'content' => ['required', 'string', 'max:5000'],

            // カテゴリー（任意：未指定なら Service 側でデフォルトに寄せる）
            'category' => ['nullable', 'string', 'max:50'],

            // タグ（任意・4〜10個）
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '質問タイトルは必須です。',
            'content.required' => '質問内容は必須です。',
            'content.max' => '質問内容は5000文字以内で入力してください。',
            'tags.array' => 'タグの形式が不正です。',
            'tags.*.max' => 'タグは50文字以内で入力してください。',
        ];
    }
}

