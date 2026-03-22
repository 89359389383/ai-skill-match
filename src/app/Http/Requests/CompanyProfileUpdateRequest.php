<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CompanyProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可チェック（true のため、このリクエストは誰でも送信可能。必要ならログイン必須などに変更）
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // アイコンは未変更なら送られてこない（DBの既存アイコンを維持）
            'icon' => ['nullable', 'file', 'image', 'max:5120'],
            'overview' => ['nullable', 'string', 'max:2000'],
            'contact_name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'introduction' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '会社名は必須です。',
            'name.string' => '会社名は文字列で入力してください。',
            'name.max' => '会社名は255文字以内で入力してください。',

            'icon.file' => 'アイコン画像はファイルを選択してください。',
            'icon.image' => 'アイコン画像は画像ファイルを選択してください。',
            'icon.max' => 'アイコン画像は5MB以下のファイルを選択してください。',

            'overview.string' => '概要は文字列で入力してください。',
            'overview.max' => '概要は2000文字以内で入力してください。',
            'contact_name.required' => '担当者名を入力してください。',
            'contact_name.string' => '担当者名は文字列で入力してください。',
            'contact_name.max' => '担当者名は255文字以内で入力してください。',
            'department.string' => '部署は文字列で入力してください。',
            'department.max' => '部署は255文字以内で入力してください。',
            'introduction.string' => '紹介文は文字列で入力してください。',
            'introduction.max' => '紹介文は2000文字以内で入力してください。',
        ];
    }

    protected function passedValidation(): void
    {
        $icon = $this->file('icon');
        Log::info('[企業登録/設定] CompanyProfileUpdateRequest バリデーション成功', [
            'user_id' => $this->user()?->id,
            'name_length' => mb_strlen((string) $this->input('name', '')),
            'contact_name_length' => mb_strlen((string) $this->input('contact_name', '')),
            'icon_original_name' => $icon?->getClientOriginalName(),
            'icon_size_bytes' => $icon?->getSize(),
            'icon_client_mime' => $icon?->getClientMimeType(),
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::warning('[企業登録/設定] CompanyProfileUpdateRequest バリデーション失敗', [
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'error_fields' => array_keys($validator->errors()->toArray()),
            'messages' => $validator->errors()->toArray(),
        ]);

        parent::failedValidation($validator);
    }
}

