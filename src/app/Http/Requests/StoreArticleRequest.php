<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルート側で auth.any をかけている前提。
        // ここでは「必須項目を満たしているか」を保証する。
        return true;
    }

    public function rules(): array
    {
        return [
            // タイトル（必須）
            'title' => ['required', 'string', 'max:255'],

            // 概要（必須・最大200）
            'excerpt' => ['required', 'string', 'max:200'],

            // カテゴリー（必須）
            'category' => ['required', 'string', 'max:50'],

            // 画像URL（任意）
            'eyecatch_image_url' => ['nullable', 'url'],

            // 記事の構造（大項目/中項目）: JSONとして保存するので配列を許可
            'structure' => ['nullable', 'array'],

            // タグ（任意）
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'excerpt.required' => '概要は必須です。',
            'excerpt.max' => '概要は200文字以内で入力してください。',
            'category.required' => 'カテゴリーは必須です。',
            'eyecatch_image_url.url' => 'アイキャッチ画像URLは正しいURL形式で入力してください。',
            'structure.array' => '記事構造の形式が不正です。',
            'tags.array' => 'タグの形式が不正です。',
            'tags.*.max' => 'タグは50文字以内で入力してください。',
        ];
    }
}

