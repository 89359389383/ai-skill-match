<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillTransactionCompleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可（購入者かどうか等）は Controller 側で実施する
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価（星）を選択してください。',
            'rating.integer' => '評価（星）は数字で指定してください。',
            'rating.min' => '評価（星）は1〜5で選択してください。',
            'rating.max' => '評価（星）は1〜5で選択してください。',
            'review.string' => 'コメントは文字列で入力してください。',
            'review.max' => 'コメントは2000文字以内で入力してください。',
        ];
    }
}

