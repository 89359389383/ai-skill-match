<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルート側で auth:freelancer + freelancer をかけている前提。
        // ここでは「入力の形」を保証することに集中する。
        return true;
    }

    public function rules(): array
    {
        return [
            // タイトル（必須・最大100）
            'title' => ['required', 'string', 'max:100'],

            // 説明（必須）
            'description' => ['required', 'string'],

            // 価格（必須・0以上）
            'price' => ['required', 'integer', 'min:0'],

            // 価格タイプ（任意：固定 or 時間単位）
            'pricing_type' => ['nullable', 'in:fixed,hourly'],

            // サムネURL（任意）
            'thumbnail_url' => ['nullable', 'url'],

            // 目安納期（任意）
            'delivery_days' => ['nullable', 'integer', 'min:1'],

            // 共通スキルID（任意・複数）
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],

            // 添付（任意）
            'assets' => ['nullable', 'array'],
            'assets.*.type' => ['nullable', 'in:image,file'],
            'assets.*.url' => ['required_with:assets', 'string'],
            'assets.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは100文字以内で入力してください。',
            'description.required' => '説明文は必須です。',
            'price.required' => '価格は必須です。',
            'price.integer' => '価格は整数で入力してください。',
            'price.min' => '価格は0以上で入力してください。',
            'pricing_type.in' => '価格タイプの指定が不正です。',
            'thumbnail_url.url' => 'サムネイルURLは正しいURL形式で入力してください。',
            'delivery_days.integer' => '目安納期は整数で入力してください。',
            'delivery_days.min' => '目安納期は1以上で入力してください。',
            'skill_ids.array' => 'スキルの形式が不正です。',
            'skill_ids.*.exists' => '選択したスキルが存在しません。',
            'assets.array' => '添付の形式が不正です。',
            'assets.*.type.in' => '添付の種類が不正です。',
            'assets.*.url.required_with' => '添付URLは必須です。',
        ];
    }
}

