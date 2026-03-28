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

        // DB が NOT NULL（stringカラム）なので、UI から未送信でも空文字で埋める
        $this->merge([
            'excerpt' => (string) $this->input('excerpt', ''),
        ]);
    }

    public function rules(): array
    {
        return [
            // タイトル（必須）
            'title' => ['required', 'string', 'max:255'],

            // 概要（任意・最大200）
            'excerpt' => ['nullable', 'string', 'max:200'],

            // カテゴリー（必須）
            'category' => ['required', 'string', 'max:50'],

            // 画像URL（任意）
            'eyecatch_image_url' => ['nullable', 'url'],
            // アイキャッチ画像ファイル（必須、最大5MB）
            'eyecatch_image' => ['required', 'file', 'image', 'max:5120'],

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

            // タグ（任意・4〜10個）
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],

            // 公開設定（任意・デフォルトは公開）
            'is_published' => ['nullable', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'excerpt.max' => '概要は200文字以内で入力してください。',
            'category.required' => 'カテゴリーは必須です。',
            'eyecatch_image_url.url' => 'アイキャッチ画像URLは正しいURL形式で入力してください。',
            'eyecatch_image.required' => 'アイキャッチ画像は必須です。',
            'body_html.required' => '本文を入力してください。',
            'body_html.max' => '本文が長すぎます。',
            'structure.array' => '記事構造の形式が不正です。',
            'tags.array' => 'タグの形式が不正です。',
            'tags.*.max' => 'タグは50文字以内で入力してください。',
            'is_published.in' => '公開設定の値が不正です。',
        ];
    }
}

