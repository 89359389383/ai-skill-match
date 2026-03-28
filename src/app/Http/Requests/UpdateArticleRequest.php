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

        // DB が NOT NULL（stringカラム）なので、UI から未送信の場合は既存値を維持
        if (! $this->has('excerpt')) {
            $routeArticle = $this->route('article');
            $existing = null;
            if (is_object($routeArticle) && property_exists($routeArticle, 'excerpt')) {
                $existing = $routeArticle->excerpt;
            }
            $this->merge([
                'excerpt' => (string) ($existing ?? ''),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:50'],
            'eyecatch_image_url' => ['nullable', 'url'],
            'eyecatch_image' => ['nullable', 'file', 'image', 'max:5120'],
            // 既存アイキャッチ画像を「×」で削除したい場合のフラグ（更新時のみ使用）
            'eyecatch_image_remove' => ['nullable', 'in:0,1'],
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
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],

            // 公開設定（任意）
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
            'eyecatch_image.image' => 'アイキャッチ画像は画像ファイルを選択してください。',
            'eyecatch_image.max' => 'アイキャッチ画像は5MB以内にしてください。',
            'body_html.required' => '本文を入力してください。',
            'body_html.max' => '本文が長すぎます。',
            'tags.array' => 'タグの形式が不正です。',
            'tags.*.max' => 'タグは50文字以内で入力してください。',
            'is_published.in' => '公開設定の値が不正です。',
        ];
    }
}

