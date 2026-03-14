<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルート側で auth.any をかけている前提。
        return true;
    }

    public function rules(): array
    {
        return [
            // 回答本文（必須）
            'content' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '回答内容は必須です。',
            'content.max' => '回答内容は5000文字以内で入力してください。',
        ];
    }
}

