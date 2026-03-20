<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DirectMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'メッセージを入力してください。',
            'content.string' => 'メッセージは文字列で入力してください。',
            'content.max' => 'メッセージは2000文字以内で入力してください。',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('body') && !$this->has('content')) {
            $this->merge([
                'content' => $this->input('body'),
            ]);
        }
    }
}
