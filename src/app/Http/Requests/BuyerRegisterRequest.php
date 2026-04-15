<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyerRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'file', 'image', 'max:5120'],
            'age_group' => ['nullable', 'string', 'max:50'],
            'prefecture' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'display_name.required' => '表示名を入力してください。',
            'display_name.string' => '表示名は文字列で入力してください。',
            'display_name.max' => '表示名は255文字以内で入力してください。',

            'icon.file' => 'アイコン画像はファイルを選択してください。',
            'icon.image' => 'アイコン画像は画像ファイルを選択してください。',
            'icon.max' => 'アイコン画像は5MB以下のファイルを選択してください。',

            'age_group.string' => '年代は文字列で入力してください。',
            'age_group.max' => '年代は50文字以内で入力してください。',

            'prefecture.string' => '都道府県は文字列で入力してください。',
            'prefecture.max' => '都道府県は50文字以内で入力してください。',

            'address.string' => '住所は文字列で入力してください。',
            'address.max' => '住所は2000文字以内で入力してください。',
        ];
    }
}

