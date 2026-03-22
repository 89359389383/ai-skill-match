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
            // アイキャッチ画像ファイル（任意、最大5MB）
            'eyecatch_image' => ['nullable', 'file', 'image', 'max:5120'],

            // 本文（Quill の HTML）
            'body_html' => [
                'required',
                'string',
                'max:50000',
                function ($attribute, $value, $fail) {
                    if (trim(strip_tags($value)) === '') {
                        $fail('本文を入力してください。');
                    }
                },
            ],

            // 記事の構造（旧フォーム互換・任意）
            'structure' => ['nullable', 'array'],
            'structure.*.title' => ['nullable', 'string', 'max:255'],
            'structure.*.subsections' => ['nullable', 'array'],
            'structure.*.subsections.*.title' => ['nullable', 'string', 'max:255'],
            'structure.*.subsections.*.content' => ['nullable', 'string'],

            // タグ（必須・4〜16）
            'tags' => ['required', 'array', 'min:4', 'max:16'],
            'tags.*' => ['string', 'max:50'],

            // 公開設定（任意・デフォルトは公開）
            'is_published' => ['nullable', 'in:0,1'],
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
            'body_html.required' => '本文を入力してください。',
            'body_html.max' => '本文が長すぎます。',
            'structure.array' => '記事構造の形式が不正です。',
            'tags.array' => 'タグの形式が不正です。',
            'tags.min' => 'タグは最低4個入力してください。',
            'tags.max' => 'タグは最大16個までです。',
            'tags.*.max' => 'タグは50文字以内で入力してください。',
            'is_published.in' => '公開設定の値が不正です。',
        ];
    }
}

