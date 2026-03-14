<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Job;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可チェック（true のため、このリクエストは誰でも送信可能。必要ならログイン必須などに変更）
        return true;
    }

    public function rules(): array
    {
        return [
            // 案件名（必須）
            'title' => ['required', 'string', 'max:255'],
            // サブタイトル（必須）
            'subtitle' => ['required', 'string', 'max:255'],
            // 説明（必須）
            'description' => ['required', 'string', 'max:5000'],
            // 求めている人物像（必須）
            'desired_persona' => ['required', 'string', 'max:5000'],
            // 必須スキル（任意）
            'required_skills_text' => ['nullable', 'string', 'max:500'],
            // 報酬種別（必須）
            'reward_type' => ['required', 'string', 'in:monthly,hourly'],
            // 報酬レンジ（必須）
            'min_rate' => ['required', 'integer', 'min:0'],
            'max_rate' => ['required', 'integer', 'min:0', 'gte:min_rate'],
            // 稼働条件（必須）
            'work_time_text' => ['required', 'string', 'max:100'],
            // 稼働開始/掲載終了（必須）
            'work_start_date' => ['required', 'date'],
            'publish_end_date' => ['required', 'date', 'after_or_equal:work_start_date'],
            // ステータス（必須）
            'status' => ['required', 'integer', 'in:' . Job::STATUS_DRAFT . ',' . Job::STATUS_PUBLISHED . ',' . Job::STATUS_STOPPED],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '案件名を入力してください。',
            'title.string' => '案件名は文字列で入力してください。',
            'title.max' => '案件名は255文字以内で入力してください。',

            'subtitle.required' => 'サブタイトルを入力してください。',
            'subtitle.string' => 'サブタイトルは文字列で入力してください。',
            'subtitle.max' => 'サブタイトルは255文字以内で入力してください。',

            'description.required' => '説明を入力してください。',
            'description.string' => '説明は文字列で入力してください。',
            'description.max' => '説明は5000文字以内で入力してください。',

            'desired_persona.required' => '求めている人物像を入力してください。',
            'desired_persona.string' => '求めている人物像は文字列で入力してください。',
            'desired_persona.max' => '求めている人物像は5000文字以内で入力してください。',

            'required_skills_text.string' => '必須スキルは文字列で入力してください。',
            'required_skills_text.max' => '必須スキルは500文字以内で入力してください。',

            'reward_type.required' => '報酬タイプを選択してください。',
            'reward_type.string' => '報酬タイプは文字列で入力してください。',
            'reward_type.in' => '報酬タイプは「月額/案件単価」または「時給」を選択してください。',

            'min_rate.required' => '最低単価を入力してください。',
            'min_rate.integer' => '最低単価は整数で入力してください。',
            'min_rate.min' => '最低単価は0以上で入力してください。',

            'max_rate.required' => '最高単価を入力してください。',
            'max_rate.integer' => '最高単価は整数で入力してください。',
            'max_rate.min' => '最高単価は0以上で入力してください。',
            'max_rate.gte' => '最高単価は最低単価以上で入力してください。',

            'work_time_text.required' => '稼働条件を入力してください。',
            'work_time_text.string' => '稼働条件は文字列で入力してください。',
            'work_time_text.max' => '稼働条件は100文字以内で入力してください。',

            'work_start_date.required' => '稼働開始を入力してください。',
            'work_start_date.date' => '稼働開始は日付形式で入力してください。',
            'publish_end_date.required' => '掲載終了を入力してください。',
            'publish_end_date.date' => '掲載終了は日付形式で入力してください。',
            'publish_end_date.after_or_equal' => '掲載終了は稼働開始以降の日付を入力してください。',

            'status.required' => 'ステータスを指定してください。',
            'status.integer' => 'ステータスの指定が不正です。',
            'status.in' => 'ステータスの指定が不正です。',
        ];
    }
}

