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
            'description' => ['required', 'string', 'max:5000'],

            // 価格（必須・0以上）
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],

            // 価格タイプ（任意：固定 or 時間単位）
            'pricing_type' => ['nullable', 'in:fixed,hourly'],

            // サムネイルアップロード（任意）
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:5120'],

            // サムネイル削除フラグ（編集時のみ想定）
            'remove_thumbnail' => ['nullable', 'boolean'],

            // サムネURL（後方互換: 従来の受け口）
            'thumbnail_url' => ['nullable', 'url', 'max:255'],

            // 目安納期（必須）
            'delivery_days' => ['required', 'integer', 'min:1', 'max:365'],

            // 共通スキル名（UIはタグ名で来るため）: 4〜16
            'skill_names' => ['required', 'array', 'min:4', 'max:16'],
            'skill_names.*' => ['nullable', 'string', 'max:100'],

            // 共通スキルID（後方互換）
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
            'description.max' => '説明文は5000文字以内で入力してください。',
            'price.required' => '価格は必須です。',
            'price.integer' => '価格は整数で入力してください。',
            'price.min' => '価格は0以上で入力してください。',
            'price.max' => '価格は100,000,000以下で入力してください。',
            'pricing_type.in' => '価格タイプの指定が不正です。',
            'thumbnail.image' => 'サムネイルは画像ファイルを指定してください。',
            'thumbnail.mimes' => 'サムネイルは jpg/jpeg/png/gif のいずれかを指定してください。',
            'thumbnail.max' => 'サムネイルサイズは5120KB以下にしてください。',
            'remove_thumbnail.boolean' => 'サムネイル削除フラグの指定が不正です。',
            'thumbnail_url.url' => 'サムネイルURLは正しいURL形式で入力してください。',
            'thumbnail_url.max' => 'サムネイルURLは255文字以内で入力してください。',
            'delivery_days.required' => '納期は必須です。',
            'delivery_days.integer' => '目安納期は整数で入力してください。',
            'delivery_days.min' => '目安納期は1以上で入力してください。',
            'delivery_days.max' => '目安納期は365日以内で入力してください。',
            'skill_names.array' => 'スキル名の形式が不正です。',
            'skill_names.min' => 'スキル名は最低4個入力してください。',
            'skill_names.max' => 'スキル名は最大16個までです。',
            'skill_names.*.string' => 'スキル名の形式が不正です。',
            'skill_ids.array' => 'スキルの形式が不正です。',
            'skill_ids.*.exists' => '選択したスキルが存在しません。',
            'assets.array' => '添付の形式が不正です。',
            'assets.*.type.in' => '添付の種類が不正です。',
            'assets.*.url.required_with' => '添付URLは必須です。',
        ];
    }
}

