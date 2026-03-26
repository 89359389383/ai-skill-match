<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CompanyRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可チェック（true のため、このリクエストは誰でも送信可能。必要ならログイン必須などに変更）
        return true;
    }

    public function rules(): array
    {
        return [
            // 会社名（必須・文字列・255文字以内）
            'company_name' => ['required', 'string', 'max:255'],
            // アイコンはファイル選択または一時データのどちらかが必要
            'icon' => ['nullable', 'file', 'image', 'max:5120', 'required_without:icon_data'],
            // アイコンの一時データ（リロード後の再表示と再送信用）
            'icon_data' => ['nullable', 'string', 'required_without:icon'],
            // 会社概要（任意・文字列・2000文字以内）
            'overview' => ['nullable', 'string', 'max:2000'],
            // 担当者名（必須・文字列・255文字以内）
            'contact_name' => ['required', 'string', 'max:255'],
            // 部署名（任意・文字列・255文字以内）
            'department' => ['nullable', 'string', 'max:255'],
            // 紹介文/メッセージ（任意・文字列・2000文字以内）
            'introduction' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => '企業名を入力してください。',
            'company_name.string' => '企業名は文字列で入力してください。',
            'company_name.max' => '企業名は255文字以内で入力してください。',

            'icon.required_without' => 'アイコン画像を選択してください。',
            'icon.file' => 'アイコン画像はファイルを選択してください。',
            'icon.image' => 'アイコン画像は画像ファイルを選択してください。',
            'icon.max' => 'アイコン画像は5MB以下のファイルを選択してください。',
            'icon_data.required_without' => 'アイコン画像を選択してください。',

            'overview.string' => '会社概要は文字列で入力してください。',
            'overview.max' => '会社概要は2000文字以内で入力してください。',

            'contact_name.required' => '担当者名を入力してください。',
            'contact_name.string' => '担当者名は文字列で入力してください。',
            'contact_name.max' => '担当者名は255文字以内で入力してください。',

            'department.string' => '部署名は文字列で入力してください。',
            'department.max' => '部署名は255文字以内で入力してください。',

            'introduction.string' => '自己紹介は文字列で入力してください。',
            'introduction.max' => '自己紹介は2000文字以内で入力してください。',
        ];
    }

    protected function passedValidation(): void
    {
        $icon = $this->file('icon');
        Log::info('[企業登録] プロフィール CompanyRegisterRequest バリデーション成功', [
            'user_id' => $this->user()?->id,
            'company_name_length' => mb_strlen((string) $this->input('company_name', '')),
            'overview_length' => mb_strlen((string) $this->input('overview', '')),
            'contact_name_length' => mb_strlen((string) $this->input('contact_name', '')),
            'department_length' => mb_strlen((string) $this->input('department', '')),
            'introduction_length' => mb_strlen((string) $this->input('introduction', '')),
            'icon_original_name' => $icon?->getClientOriginalName(),
            'icon_client_mime' => $icon?->getClientMimeType(),
            'icon_size_bytes' => $icon?->getSize(),
            'icon_valid' => $icon?->isValid(),
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::warning('[企業登録] プロフィール CompanyRegisterRequest バリデーション失敗', [
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'error_fields' => array_keys($validator->errors()->toArray()),
            'messages' => $validator->errors()->toArray(),
        ]);

        parent::failedValidation($validator);
    }
}

