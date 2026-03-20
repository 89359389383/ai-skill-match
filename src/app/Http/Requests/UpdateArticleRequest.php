<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // “本人のみ” の権限チェックは Controller（ensureOwner）で行う。
        // FormRequest は入力のバリデーションに集中する。
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:50'],
            'eyecatch_image_url' => ['nullable', 'url'],
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
            'structure' => ['nullable', 'array'],
            'tags' => ['nullable', 'array', 'max:5'],
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
            'body_html.required' => '本文を入力してください。',
            'body_html.max' => '本文が長すぎます。',
            'tags.max' => 'タグは最大5個までです。',
        ];
    }
}

