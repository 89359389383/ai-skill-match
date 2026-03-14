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

    public function rules(): array
    {
        return [
            // タイトル（必須）
            'title' => ['required', 'string', 'max:255'],

            // 本文（必須）
            'content' => ['required', 'string'],

            // カテゴリー（任意：未指定なら Service 側でデフォルトに寄せる）
            'category' => ['nullable', 'string', 'max:50'],

            // タグ（任意）
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '質問タイトルは必須です。',
            'content.required' => '質問内容は必須です。',
            'tags.array' => 'タグの形式が不正です。',
            'tags.*.max' => 'タグは50文字以内で入力してください。',
        ];
    }
}

