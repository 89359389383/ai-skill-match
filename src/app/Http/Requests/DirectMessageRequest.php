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
            // =========================
            // メッセージ本文
            // =========================
            'content' => [
                'nullable',
                'string',
                'max:2000',
                'required_without:attachments',
            ],

            // =========================
            // 添付ファイル（配列）
            // =========================
            'attachments' => [
                'nullable',
                'array',
                'max:3',
                'required_without:content',

                // 合計30MBチェック
                function ($attribute, $value, $fail) {
                    if (!is_array($value)) {
                        return;
                    }

                    $total = 0;

                    foreach ($value as $file) {
                        // 👇空はスキップ（超重要）
                        if (!$file || !$file->isValid()) {
                            continue;
                        }

                        $total += $file->getSize();
                    }

                    $limitBytes = 30 * 1024 * 1024; // 30MB

                    if ($total > $limitBytes) {
                        $fail('添付ファイルの合計サイズは30MB以内にしてください。');
                    }
                },
            ],

            // =========================
            // 各ファイル
            // =========================
            'attachments.*' => [
                'nullable', // 👈超重要（空を許可）
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,jpg,jpeg,png,gif,webp',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // content
            'content.required_without' => 'メッセージまたは添付ファイルを入力してください。',
            'content.string' => 'メッセージは文字列で入力してください。',
            'content.max' => 'メッセージは2000文字以内で入力してください。',

            // attachments
            'attachments.required_without' => 'メッセージまたは添付ファイルを選択してください。',
            'attachments.array' => '添付ファイルの形式が不正です。',
            'attachments.max' => '添付できるファイルは最大3つまでです。',

            // attachments.*
            'attachments.*.file' => '添付ファイルの形式が不正です。',
            'attachments.*.max' => '添付ファイルは10MB以内にしてください。',
            'attachments.*.mimes' => '添付できる拡張子は pdf/doc/docx/xls/xlsx/ppt/pptx/txt/csv/zip/jpg/jpeg/png/gif/webp です。',
        ];
    }

    protected function prepareForValidation(): void
    {
        // body → content に変換（互換対応）
        if ($this->has('body') && !$this->has('content')) {
            $this->merge([
                'content' => $this->input('body'),
            ]);
        }
    }
}