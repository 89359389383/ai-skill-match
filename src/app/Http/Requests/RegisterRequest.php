<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可チェック（true のため、このリクエストは誰でも送信可能。必要ならログイン必須などに変更）
        return true;
    }

    public function rules(): array
    {
        return [
            // 登録用メールアドレス（必須・メール形式・users.email で重複不可）
            'email' => ['required', 'email', 'unique:users,email'],
            // 登録用パスワード（必須・8文字以上・確認用パスワードと一致）
            'password' => ['required', 'string', 'min:8', 'max:128', 'confirmed'],
            // 確認用パスワード（必須）
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'email.unique' => 'このメールアドレスは既に登録されています。',

            'password.required' => 'パスワードを入力してください。',
            'password.string' => 'パスワードは文字列で入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.max' => 'パスワードは128文字以下で入力してください。',
            'password.confirmed' => 'パスワード（確認）が一致しません。',

            'password_confirmation.required' => 'パスワード（確認）を入力してください。',
        ];
    }

    protected function passedValidation(): void
    {
        $routeName = $this->route()?->getName();
        if (!in_array($routeName, ['auth.register.company.store', 'auth.register.freelancer.store'], true)) {
            return;
        }

        $isCompany = $routeName === 'auth.register.company.store';
        Log::info($isCompany ? '[企業登録] RegisterRequest バリデーション成功' : '[フリーランス登録] RegisterRequest バリデーション成功', [
            'route' => $routeName,
            'ip' => $this->ip(),
            'email' => $this->input('email'),
            'password_length' => strlen((string) $this->input('password', '')),
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        $routeName = $this->route()?->getName();
        if ($routeName === 'auth.register.company.store') {
            Log::warning('[企業登録] RegisterRequest バリデーション失敗', [
                'route' => $routeName,
                'ip' => $this->ip(),
                'error_fields' => array_keys($validator->errors()->toArray()),
                'messages' => $validator->errors()->toArray(),
            ]);
        }

        parent::failedValidation($validator);
    }
}

