<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルート側で auth.any をかけている前提。
        return true;
    }

    public function rules(): array
    {
        return [
            // 問い合わせ本文（必須）
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => '問い合わせ内容を入力してください。',
            'message.max' => '問い合わせ内容は2000文字以内で入力してください。',
        ];
    }
}

