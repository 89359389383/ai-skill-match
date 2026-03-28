<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可チェック（true のため、このリクエストは誰でも送信可能。必要ならログイン必須などに変更）
        return true;
    }

    public function rules(): array
    {
        return [
            // メッセージ本文（nullable・添付がある場合は必須不要）
            'content' => ['nullable', 'string', 'max:2000', 'required_without:attachments'],

            // 添付ファイル（最大3つ、合計10MB以内）
            'attachments' => [
                'nullable',
                'array',
                'max:3',
                'required_without:content',
                function ($attribute, $value, $fail) {
                    if ($value === null) return;
                    if (!is_array($value)) return;

                    $total = 0;
                    foreach ($value as $file) {
                        if (!$file || !is_object($file) || !method_exists($file, 'isValid')) continue;
                        if (!$file->isValid()) continue;
                        $total += (int) $file->getSize();
                    }

                    $limitBytes = 10 * 1024 * 1024; // 10MB
                    if ($total > $limitBytes) {
                        $fail('添付ファイルの合計サイズは10MB以内にしてください。');
                    }
                },
            ],
            'attachments.*' => [
                'nullable',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,jpg,jpeg,png,gif,webp',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required_without' => 'メッセージ本文を入力してください。',
            'content.string' => 'メッセージ本文は文字列で入力してください。',
            'content.max' => 'メッセージ本文は2000文字以内で入力してください。',

            'attachments.required_without' => 'メッセージ本文または添付ファイルを入力してください。',
            'attachments.array' => '添付ファイルの形式が不正です。',
            'attachments.max' => '添付できるファイルは最大3つまでです。',
            'attachments.*.file' => '添付ファイルの形式が不正です。',
            'attachments.*.max' => '添付ファイルは10MB以内にしてください。',
            'attachments.*.mimes' => '添付できる拡張子は pdf/doc/docx/xls/xlsx/ppt/pptx/txt/csv/zip/jpg/jpeg/png/gif/webp です。',
        ];
    }

    protected function prepareForValidation(): void
    {
        // 互換用：body で送られてきた場合は content に寄せる
        if ($this->has('body') && !$this->has('content')) {
            $this->merge([
                'content' => $this->input('body'),
            ]);
        }
    }
}

