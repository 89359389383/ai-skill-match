<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FreelancerRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可チェック（true のため、このリクエストは誰でも送信可能。必要ならログイン必須などに変更）
        return true;
    }

    public function rules(): array
    {
        return [
            // 基本プロフィール
            'display_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'max:5000'],

            // 稼働条件
            'min_hours_per_week' => ['required', 'integer', 'min:0', 'max:168'],
            'max_hours_per_week' => ['required', 'integer', 'min:0', 'max:168'],
            'hours_per_day' => ['required', 'integer', 'min:0', 'max:24'],
            'days_per_week' => ['required', 'integer', 'min:0', 'max:7'],

            // 働き方・単価（任意）
            'work_style_text' => ['nullable', 'string', 'max:5000'],
            'min_rate' => ['nullable', 'integer', 'min:0'],
            'max_rate' => ['nullable', 'integer', 'min:0', 'gte:min_rate'],

            // その他（任意）
            'experience_companies' => ['nullable', 'string', 'max:5000'],
            'icon' => ['nullable', 'file', 'image', 'max:5120'],

            // スキル関連（任意）
            'skills' => ['sometimes', 'array', 'min:1'],
            'skills.*' => ['integer'],
            'custom_skills' => ['required_without:skills', 'array', 'min:1'],
            'custom_skills.*' => ['required', 'string', 'max:255'],

            // ポートフォリオURL（任意）
            'portfolio_urls' => ['sometimes', 'array'],
            'portfolio_urls.*' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * DB側（freelancers.min_rate/max_rate）がNOT NULLのため、
     * 未入力でも保存できるように値を補完する。
     *
     * - 両方未入力: 0/0（=未設定扱い）
     * - 片方のみ入力: もう片方に同値を補完
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        $min = $this->input('min_rate');
        $max = $this->input('max_rate');

        if ($min === null && $max === null) {
            $merge['min_rate'] = 0;
            $merge['max_rate'] = 0;
        } elseif ($min === null && $max !== null) {
            $merge['min_rate'] = $max;
        } elseif ($max === null && $min !== null) {
            $merge['max_rate'] = $min;
        }

        // custom_skills[] は空欄が複数送られてくるので、空文字を除去してからバリデーションする
        $customSkills = $this->input('custom_skills');
        if (is_array($customSkills)) {
            $normalized = [];
            foreach ($customSkills as $skill) {
                if (!is_string($skill)) {
                    continue;
                }
                $skill = trim($skill);
                if ($skill === '') {
                    continue;
                }
                $normalized[] = $skill;
            }
            $merge['custom_skills'] = $normalized;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function messages(): array
    {
        return [
            'display_name.required' => '表示名を入力してください。',
            'display_name.string' => '表示名は文字列で入力してください。',
            'display_name.max' => '表示名は255文字以内で入力してください。',

            'job_title.required' => '職種を入力してください。',
            'job_title.string' => '職種は文字列で入力してください。',
            'job_title.max' => '職種は255文字以内で入力してください。',

            'bio.required' => '自己紹介文を入力してください。',
            'bio.string' => '自己紹介文は文字列で入力してください。',
            'bio.max' => '自己紹介文は5000文字以内で入力してください。',

            'min_hours_per_week.required' => '週の稼働時間（下限）を入力してください。',
            'min_hours_per_week.integer' => '週の稼働時間（下限）は整数で入力してください。',
            'min_hours_per_week.min' => '週の稼働時間（下限）は0以上で入力してください。',
            'min_hours_per_week.max' => '週の稼働時間（下限）は168以下で入力してください。',

            'max_hours_per_week.required' => '週の稼働時間（上限）を入力してください。',
            'max_hours_per_week.integer' => '週の稼働時間（上限）は整数で入力してください。',
            'max_hours_per_week.min' => '週の稼働時間（上限）は0以上で入力してください。',
            'max_hours_per_week.max' => '週の稼働時間（上限）は168以下で入力してください。',

            'hours_per_day.required' => '1日の稼働時間を入力してください。',
            'hours_per_day.integer' => '1日の稼働時間は整数で入力してください。',
            'hours_per_day.min' => '1日の稼働時間は0以上で入力してください。',
            'hours_per_day.max' => '1日の稼働時間は24以下で入力してください。',

            'days_per_week.required' => '週の稼働日数を入力してください。',
            'days_per_week.integer' => '週の稼働日数は整数で入力してください。',
            'days_per_week.min' => '週の稼働日数は0以上で入力してください。',
            'days_per_week.max' => '週の稼働日数は7以下で入力してください。',

            'work_style_text.string' => '働き方は文字列で入力してください。',
            'work_style_text.max' => '働き方は5000文字以内で入力してください。',

            'min_rate.integer' => '希望単価（下限）は整数で入力してください。',
            'min_rate.min' => '希望単価（下限）は0以上で入力してください。',

            'max_rate.integer' => '希望単価（上限）は整数で入力してください。',
            'max_rate.min' => '希望単価（上限）は0以上で入力してください。',
            'max_rate.gte' => '希望単価（上限）は希望単価（下限）以上で入力してください。',

            'experience_companies.string' => '経験企業は文字列で入力してください。',
            'experience_companies.max' => '経験企業は5000文字以内で入力してください。',

            'icon.file' => 'アイコン画像はファイルを選択してください。',
            'icon.image' => 'アイコン画像は画像ファイルを選択してください。',
            'icon.max' => 'アイコン画像は5MB以下のファイルを選択してください。',

            'skills.array' => 'スキルは配列形式で送信してください。',
            'skills.*.integer' => 'スキルの指定が不正です。',

            'custom_skills.array' => '自由入力スキルは配列形式で送信してください。',
            'custom_skills.required_without' => 'スキルを1つ以上入力してください。',
            'custom_skills.min' => 'スキルを1つ以上入力してください。',
            'custom_skills.*.required' => 'スキルを入力してください。',
            'custom_skills.*.string' => 'スキルは文字列で入力してください。',
            'custom_skills.*.max' => 'スキルは255文字以内で入力してください。',

            'portfolio_urls.array' => 'ポートフォリオURLは配列形式で送信してください。',
            'portfolio_urls.*.string' => 'ポートフォリオURLは文字列で入力してください。',
            'portfolio_urls.*.max' => 'ポートフォリオURLは2000文字以内で入力してください。（空欄も可）',
        ];
    }
}

