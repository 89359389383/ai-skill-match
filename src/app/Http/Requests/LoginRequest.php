<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可チェック（true のため、このリクエストは誰でも送信可能。必要ならログイン必須などに変更）
        return true;
    }

    public function rules(): array
    {
        return [
            // ログイン用メールアドレス（必須・メール形式）
            'email' => ['required', 'email'],
            // ログイン用パスワード（必須）
            'password' => ['required', 'string', 'min:8', 'max:128'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスの形式が正しくありません。',

            'password.required' => 'パスワードを入力してください。',
            'password.string' => 'パスワードは文字列で入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.max' => 'パスワードは128文字以下で入力してください。',
        ];
    }
}

