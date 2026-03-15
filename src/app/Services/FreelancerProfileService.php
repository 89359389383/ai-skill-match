<?php

namespace App\Services;

use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FreelancerProfileService
{
    /**
     * フリーランスプロフィールを登録する
     *
     * 設計根拠（FreelancerProfileService 詳細設計）
     * - 複数テーブル（freelancers / freelancer_skill / freelancer_custom_skills / freelancer_portfolios）をまとめて登録する
     * - 画像（アイコン）保存を含む可能性がある
     * - 途中失敗時に不整合を残さないため、トランザクションで一括保存する
     */
    public function register(User $user, array $payload): Freelancer
    {
        Log::info('【FreelancerProfileService::register】プロフィール登録処理開始', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'payload_keys' => array_keys($payload),
            'payload_summary' => [
                'has_display_name' => isset($payload['display_name']),
                'has_job_title' => isset($payload['job_title']),
                'has_bio' => isset($payload['bio']),
                'has_icon' => isset($payload['icon']),
                'icon_type' => isset($payload['icon']) ? get_class($payload['icon']) : null,
                'skills_count' => isset($payload['skills']) && is_array($payload['skills']) ? count($payload['skills']) : 0,
                'custom_skills_count' => isset($payload['custom_skills']) && is_array($payload['custom_skills']) ? count($payload['custom_skills']) : 0,
                'portfolio_urls_count' => isset($payload['portfolio_urls']) && is_array($payload['portfolio_urls']) ? count($payload['portfolio_urls']) : 0,
            ],
        ]);

        try {
            // 複数テーブル更新になるので、全体をトランザクションでまとめる
            return DB::transaction(function () use ($user, $payload): Freelancer {
                Log::debug('【FreelancerProfileService::register】トランザクション開始', [
                    'user_id' => $user->id,
                ]);

                // 既にプロフィールがある場合は再登録させない（Controllerでも防ぐが二重防御）
                $existingFreelancer = $user->freelancer()->first();
                if ($existingFreelancer) {
                    Log::warning('【FreelancerProfileService::register】既存プロフィールが存在するため、再登録をスキップ', [
                        'user_id' => $user->id,
                        'freelancer_id' => $existingFreelancer->id,
                    ]);
                    // ここでは「既存を返す」ことで、アプリを落とさず安全側に倒す
                    return $existingFreelancer;
                }

                Log::debug('【FreelancerProfileService::register】既存プロフィールチェック完了 - 新規登録を続行', [
                    'user_id' => $user->id,
                ]);

            // アイコンがある場合は保存してパスを保持する（設計：画像保存の可能性）
            $iconPath = null;
            // payloadにiconがあり、かつUploadedFileならファイルとして扱う
            if (isset($payload['icon']) && $payload['icon'] instanceof UploadedFile) {
                Log::debug('【FreelancerProfileService::register】アイコンファイル処理開始', [
                    'user_id' => $user->id,
                    'icon_original_name' => $payload['icon']->getClientOriginalName(),
                    'icon_size' => $payload['icon']->getSize(),
                    'icon_mime_type' => $payload['icon']->getMimeType(),
                    'icon_extension' => $payload['icon']->getClientOriginalExtension(),
                ]);

                try {
                    // publicディスクに保存して、保存先パスを受け取る
                    $iconPath = $payload['icon']->store('freelancer_icons', 'public');
                    Log::info('【FreelancerProfileService::register】アイコンファイル保存成功', [
                        'user_id' => $user->id,
                        'icon_path' => $iconPath,
                        'icon_size' => $payload['icon']->getSize(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('【FreelancerProfileService::register】アイコンファイル保存失敗', [
                        'user_id' => $user->id,
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                    ]);
                    throw $e;
                }
            } else {
                Log::debug('【FreelancerProfileService::register】アイコンファイルなし', [
                    'user_id' => $user->id,
                    'has_icon_key' => isset($payload['icon']),
                    'icon_type' => isset($payload['icon']) ? gettype($payload['icon']) : null,
                ]);
            }

            // freelancers テーブルへ基本情報・稼働条件などを保存する（設計：基本情報保存）
            Log::debug('【FreelancerProfileService::register】基本情報保存開始', [
                'user_id' => $user->id,
                'data_to_save' => [
                    'display_name' => $payload['display_name'] ?? null,
                    'job_title' => $payload['job_title'] ?? null,
                    'bio_length' => isset($payload['bio']) ? strlen($payload['bio']) : 0,
                    'min_hours_per_week' => $payload['min_hours_per_week'] ?? null,
                    'max_hours_per_week' => $payload['max_hours_per_week'] ?? null,
                    'hours_per_day' => $payload['hours_per_day'] ?? null,
                    'days_per_week' => $payload['days_per_week'] ?? null,
                    'has_work_style_text' => isset($payload['work_style_text']),
                    'min_rate' => $payload['min_rate'] ?? null,
                    'max_rate' => $payload['max_rate'] ?? null,
                    'has_experience_companies' => isset($payload['experience_companies']),
                    'icon_path' => $iconPath,
                ],
            ]);

            try {
                $freelancer = Freelancer::create([
                    'user_id' => $user->id,
                    'display_name' => $payload['display_name'],
                    'job_title' => $payload['job_title'],
                    'bio' => $payload['bio'],
                    'min_hours_per_week' => $payload['min_hours_per_week'],
                    'max_hours_per_week' => $payload['max_hours_per_week'],
                    'hours_per_day' => $payload['hours_per_day'],
                    'days_per_week' => $payload['days_per_week'],
                    'work_style_text' => $payload['work_style_text'] ?? null,
                    'services_offered' => $payload['services_offered'] ?? null,
                    'industry_specialties' => $payload['industry_specialties'] ?? null,
                    'prefecture' => $payload['prefecture'] ?? null,
                    'min_rate' => $payload['min_rate'] ?? null,
                    'max_rate' => $payload['max_rate'] ?? null,
                    'experience_companies' => $payload['experience_companies'] ?? null,
                    'certifications' => $payload['certifications'] ?? null,
                    'icon_path' => $iconPath,
                    'phone' => $payload['phone'] ?? null,
                    'line_id' => $payload['line_id'] ?? null,
                    'twitter_url' => $payload['twitter_url'] ?? null,
                ]);

                Log::info('【FreelancerProfileService::register】基本情報保存成功', [
                    'user_id' => $user->id,
                    'freelancer_id' => $freelancer->id,
                    'display_name' => $freelancer->display_name,
                    'created_at' => $freelancer->created_at,
                ]);
            } catch (\Exception $e) {
                Log::error('【FreelancerProfileService::register】基本情報保存失敗', [
                    'user_id' => $user->id,
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'payload_keys' => array_keys($payload),
                ]);
                throw $e;
            }

            // skills（複数選択）を中間テーブルへ登録する（設計：スキル関連付け）
            if (!empty($payload['skills']) && is_array($payload['skills'])) {
                Log::debug('【FreelancerProfileService::register】スキル登録開始', [
                    'freelancer_id' => $freelancer->id,
                    'skills_count' => count($payload['skills']),
                    'skill_ids' => $payload['skills'],
                ]);

                try {
                    // syncで中間テーブルをまとめて関連付けする
                    $freelancer->skills()->sync($payload['skills']);
                    Log::info('【FreelancerProfileService::register】スキル登録成功', [
                        'freelancer_id' => $freelancer->id,
                        'synced_skill_ids' => $payload['skills'],
                        'synced_count' => count($payload['skills']),
                    ]);
                } catch (\Exception $e) {
                    Log::error('【FreelancerProfileService::register】スキル登録失敗', [
                        'freelancer_id' => $freelancer->id,
                        'skill_ids' => $payload['skills'],
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                    ]);
                    throw $e;
                }
            } else {
                Log::debug('【FreelancerProfileService::register】スキル登録スキップ（データなし）', [
                    'freelancer_id' => $freelancer->id,
                    'has_skills_key' => isset($payload['skills']),
                    'skills_type' => isset($payload['skills']) ? gettype($payload['skills']) : null,
                    'skills_empty' => isset($payload['skills']) ? empty($payload['skills']) : null,
                ]);
            }

            // custom_skills（自由入力スキル）を登録する（設計：カスタムスキル登録）
            if (!empty($payload['custom_skills']) && is_array($payload['custom_skills'])) {
                Log::debug('【FreelancerProfileService::register】カスタムスキル登録開始', [
                    'freelancer_id' => $freelancer->id,
                    'custom_skills_raw_count' => count($payload['custom_skills']),
                    'custom_skills_raw' => $payload['custom_skills'],
                ]);

                // 表示順を管理するカウンタ
                $order = 1;
                $createdCount = 0;
                $skippedCount = 0;
                // 入力されたスキル名を順番に処理する
                foreach ($payload['custom_skills'] as $index => $skillName) {
                    // 空文字などは保存しない（入力ノイズを除去）
                    if (!is_string($skillName) || trim($skillName) === '') {
                        Log::debug('【FreelancerProfileService::register】カスタムスキルをスキップ（空文字または無効）', [
                            'freelancer_id' => $freelancer->id,
                            'index' => $index,
                            'skill_name_raw' => $skillName,
                            'skill_name_type' => gettype($skillName),
                        ]);
                        $skippedCount++;
                        continue;
                    }

                    try {
                        // 1件ずつ関連テーブルへ登録する
                        $customSkill = $freelancer->customSkills()->create([
                            // 表示用スキル名
                            'name' => trim($skillName),
                            // 画面表示順
                            'sort_order' => $order,
                        ]);

                        Log::debug('【FreelancerProfileService::register】カスタムスキル登録成功', [
                            'freelancer_id' => $freelancer->id,
                            'custom_skill_id' => $customSkill->id,
                            'skill_name' => trim($skillName),
                            'sort_order' => $order,
                            'index' => $index,
                        ]);

                        // 次の表示順へ進める
                        $order++;
                        $createdCount++;
                    } catch (\Exception $e) {
                        Log::error('【FreelancerProfileService::register】カスタムスキル登録失敗', [
                            'freelancer_id' => $freelancer->id,
                            'skill_name' => $skillName,
                            'sort_order' => $order,
                            'index' => $index,
                            'error_message' => $e->getMessage(),
                            'error_file' => $e->getFile(),
                            'error_line' => $e->getLine(),
                        ]);
                        throw $e;
                    }
                }

                Log::info('【FreelancerProfileService::register】カスタムスキル登録完了', [
                    'freelancer_id' => $freelancer->id,
                    'created_count' => $createdCount,
                    'skipped_count' => $skippedCount,
                    'total_processed' => count($payload['custom_skills']),
                ]);
            } else {
                Log::debug('【FreelancerProfileService::register】カスタムスキル登録スキップ（データなし）', [
                    'freelancer_id' => $freelancer->id,
                    'has_custom_skills_key' => isset($payload['custom_skills']),
                    'custom_skills_type' => isset($payload['custom_skills']) ? gettype($payload['custom_skills']) : null,
                    'custom_skills_empty' => isset($payload['custom_skills']) ? empty($payload['custom_skills']) : null,
                ]);
            }

            // portfolio_urls（ポートフォリオURL）を登録する（設計：ポートフォリオ登録）
            if (!empty($payload['portfolio_urls']) && is_array($payload['portfolio_urls'])) {
                Log::debug('【FreelancerProfileService::register】ポートフォリオURL登録開始', [
                    'freelancer_id' => $freelancer->id,
                    'portfolio_urls_raw_count' => count($payload['portfolio_urls']),
                    'portfolio_urls_raw' => $payload['portfolio_urls'],
                ]);

                // 表示順を管理するカウンタ
                $order = 1;
                $createdCount = 0;
                $skippedCount = 0;
                // URLを順番に処理する
                foreach ($payload['portfolio_urls'] as $index => $url) {
                    // 空文字などは保存しない（入力ノイズを除去）
                    if (!is_string($url) || trim($url) === '') {
                        Log::debug('【FreelancerProfileService::register】ポートフォリオURLをスキップ（空文字または無効）', [
                            'freelancer_id' => $freelancer->id,
                            'index' => $index,
                            'url_raw' => $url,
                            'url_type' => gettype($url),
                        ]);
                        $skippedCount++;
                        continue;
                    }

                    try {
                        // 1件ずつ関連テーブルへ登録する
                        $portfolio = $freelancer->portfolios()->create([
                            // URL本体
                            'url' => trim($url),
                            // 画面表示順
                            'sort_order' => $order,
                        ]);

                        Log::debug('【FreelancerProfileService::register】ポートフォリオURL登録成功', [
                            'freelancer_id' => $freelancer->id,
                            'portfolio_id' => $portfolio->id,
                            'url' => trim($url),
                            'sort_order' => $order,
                            'index' => $index,
                        ]);

                        // 次の表示順へ進める
                        $order++;
                        $createdCount++;
                    } catch (\Exception $e) {
                        Log::error('【FreelancerProfileService::register】ポートフォリオURL登録失敗', [
                            'freelancer_id' => $freelancer->id,
                            'url' => $url,
                            'sort_order' => $order,
                            'index' => $index,
                            'error_message' => $e->getMessage(),
                            'error_file' => $e->getFile(),
                            'error_line' => $e->getLine(),
                        ]);
                        throw $e;
                    }
                }

                Log::info('【FreelancerProfileService::register】ポートフォリオURL登録完了', [
                    'freelancer_id' => $freelancer->id,
                    'created_count' => $createdCount,
                    'skipped_count' => $skippedCount,
                    'total_processed' => count($payload['portfolio_urls']),
                ]);
            } else {
                Log::debug('【FreelancerProfileService::register】ポートフォリオURL登録スキップ（データなし）', [
                    'freelancer_id' => $freelancer->id,
                    'has_portfolio_urls_key' => isset($payload['portfolio_urls']),
                    'portfolio_urls_type' => isset($payload['portfolio_urls']) ? gettype($payload['portfolio_urls']) : null,
                    'portfolio_urls_empty' => isset($payload['portfolio_urls']) ? empty($payload['portfolio_urls']) : null,
                ]);
            }

            // 作成した freelancer を返す（Controllerはこの後リダイレクトする）
            Log::info('【FreelancerProfileService::register】プロフィール登録処理完了', [
                'user_id' => $user->id,
                'freelancer_id' => $freelancer->id,
                'display_name' => $freelancer->display_name,
                'has_icon' => !empty($freelancer->icon_path),
                'icon_path' => $freelancer->icon_path,
                'skills_count' => $freelancer->skills()->count(),
                'custom_skills_count' => $freelancer->customSkills()->count(),
                'portfolios_count' => $freelancer->portfolios()->count(),
            ]);

            Log::debug('【FreelancerProfileService::register】トランザクションコミット', [
                'user_id' => $user->id,
                'freelancer_id' => $freelancer->id,
            ]);

            return $freelancer;
            });
        } catch (\Exception $e) {
            Log::error('【FreelancerProfileService::register】プロフィール登録処理でエラー発生', [
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'payload_keys' => array_keys($payload),
            ]);
            throw $e;
        }
    }

    /**
     * フリーランスプロフィールを更新する（設定画面用）
     *
     * ※設計書（routes）に settings が存在するため、最低限の更新処理を用意する
     */
    public function update(Freelancer $freelancer, array $payload): Freelancer
    {
        Log::info('【FreelancerProfileService::update】プロフィール更新処理開始', [
            'freelancer_id' => $freelancer->id,
            'user_id' => $freelancer->user_id,
            'current_display_name' => $freelancer->display_name,
            'payload_keys' => array_keys($payload),
            'payload_summary' => [
                'has_display_name' => isset($payload['display_name']),
                'has_job_title' => isset($payload['job_title']),
                'has_bio' => isset($payload['bio']),
                'has_icon' => isset($payload['icon']),
                'icon_type' => isset($payload['icon']) ? get_class($payload['icon']) : null,
                'has_skills' => array_key_exists('skills', $payload),
                'skills_count' => isset($payload['skills']) && is_array($payload['skills']) ? count($payload['skills']) : 0,
                'has_custom_skills' => array_key_exists('custom_skills', $payload),
                'custom_skills_count' => isset($payload['custom_skills']) && is_array($payload['custom_skills']) ? count($payload['custom_skills']) : 0,
                'has_portfolio_urls' => array_key_exists('portfolio_urls', $payload),
                'portfolio_urls_count' => isset($payload['portfolio_urls']) && is_array($payload['portfolio_urls']) ? count($payload['portfolio_urls']) : 0,
            ],
        ]);

        try {
            // 更新も将来の拡張（複数テーブル更新）に備えてトランザクションでまとめる
            return DB::transaction(function () use ($freelancer, $payload): Freelancer {
                Log::debug('【FreelancerProfileService::update】トランザクション開始', [
                    'freelancer_id' => $freelancer->id,
                ]);

                // アイコン更新がある場合は、古いアイコンを削除して差し替える
                if (isset($payload['icon']) && $payload['icon'] instanceof UploadedFile) {
                    Log::debug('【FreelancerProfileService::update】アイコンファイル更新開始', [
                        'freelancer_id' => $freelancer->id,
                        'current_icon_path' => $freelancer->icon_path,
                        'new_icon_original_name' => $payload['icon']->getClientOriginalName(),
                        'new_icon_size' => $payload['icon']->getSize(),
                        'new_icon_mime_type' => $payload['icon']->getMimeType(),
                        'new_icon_extension' => $payload['icon']->getClientOriginalExtension(),
                    ]);

                    // 既存パスがあれば削除する（publicディスク想定）
                    if ($freelancer->icon_path) {
                        try {
                            // ファイルが無くてもdeleteは安全に失敗するのでそのまま呼ぶ
                            $deleted = Storage::disk('public')->delete($freelancer->icon_path);
                            Log::debug('【FreelancerProfileService::update】既存アイコンファイル削除', [
                                'freelancer_id' => $freelancer->id,
                                'old_icon_path' => $freelancer->icon_path,
                                'deleted' => $deleted,
                            ]);
                        } catch (\Exception $e) {
                            Log::warning('【FreelancerProfileService::update】既存アイコンファイル削除失敗（続行）', [
                                'freelancer_id' => $freelancer->id,
                                'old_icon_path' => $freelancer->icon_path,
                                'error_message' => $e->getMessage(),
                            ]);
                        }
                    }

                    try {
                        // 新しいアイコンを保存して、保存先パスをセットする
                        $newIconPath = $payload['icon']->store('freelancer_icons', 'public');
                        $freelancer->icon_path = $newIconPath;
                        Log::info('【FreelancerProfileService::update】アイコンファイル更新成功', [
                            'freelancer_id' => $freelancer->id,
                            'new_icon_path' => $newIconPath,
                            'new_icon_size' => $payload['icon']->getSize(),
                        ]);
                    } catch (\Exception $e) {
                        Log::error('【FreelancerProfileService::update】アイコンファイル保存失敗', [
                            'freelancer_id' => $freelancer->id,
                            'error_message' => $e->getMessage(),
                            'error_file' => $e->getFile(),
                            'error_line' => $e->getLine(),
                        ]);
                        throw $e;
                    }
                } else {
                    Log::debug('【FreelancerProfileService::update】アイコンファイル更新なし', [
                        'freelancer_id' => $freelancer->id,
                        'has_icon_key' => isset($payload['icon']),
                        'icon_type' => isset($payload['icon']) ? gettype($payload['icon']) : null,
                    ]);
                }

            // fillさせないキー（別テーブル更新用）を除外する
            $profilePayload = $payload;
            // skills/custom_skills/portfolio_urls/iconは別処理で更新するので外す
            unset($profilePayload['skills'], $profilePayload['custom_skills'], $profilePayload['portfolio_urls'], $profilePayload['icon']);

            Log::debug('【FreelancerProfileService::update】基本情報更新開始', [
                'freelancer_id' => $freelancer->id,
                'profile_payload_keys' => array_keys($profilePayload),
                'profile_payload_summary' => [
                    'has_display_name' => isset($profilePayload['display_name']),
                    'has_job_title' => isset($profilePayload['job_title']),
                    'has_bio' => isset($profilePayload['bio']),
                    'has_min_hours_per_week' => isset($profilePayload['min_hours_per_week']),
                    'has_max_hours_per_week' => isset($profilePayload['max_hours_per_week']),
                    'has_hours_per_day' => isset($profilePayload['hours_per_day']),
                    'has_days_per_week' => isset($profilePayload['days_per_week']),
                ],
                'current_values' => [
                    'display_name' => $freelancer->display_name,
                    'job_title' => $freelancer->job_title,
                    'min_hours_per_week' => $freelancer->min_hours_per_week,
                    'max_hours_per_week' => $freelancer->max_hours_per_week,
                ],
            ]);

            try {
                // それ以外のプロフィール項目を更新する（fillableの範囲で反映される）
                $freelancer->fill($profilePayload)->save();
                Log::info('【FreelancerProfileService::update】基本情報更新成功', [
                    'freelancer_id' => $freelancer->id,
                    'updated_fields' => array_keys($profilePayload),
                    'new_values' => [
                        'display_name' => $freelancer->display_name,
                        'job_title' => $freelancer->job_title,
                        'min_hours_per_week' => $freelancer->min_hours_per_week,
                        'max_hours_per_week' => $freelancer->max_hours_per_week,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('【FreelancerProfileService::update】基本情報更新失敗', [
                    'freelancer_id' => $freelancer->id,
                    'profile_payload_keys' => array_keys($profilePayload),
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                ]);
                throw $e;
            }

            // skillsが来たら同期する（設計：スキル関連付け）
            if (array_key_exists('skills', $payload) && is_array($payload['skills'])) {
                $currentSkillIds = $freelancer->skills()->pluck('skills.id')->toArray();
                Log::debug('【FreelancerProfileService::update】スキル更新開始', [
                    'freelancer_id' => $freelancer->id,
                    'current_skill_ids' => $currentSkillIds,
                    'current_skill_count' => count($currentSkillIds),
                    'new_skill_ids' => $payload['skills'],
                    'new_skill_count' => count($payload['skills']),
                ]);

                try {
                    // 中間テーブルをまとめて更新する
                    $freelancer->skills()->sync($payload['skills']);
                    Log::info('【FreelancerProfileService::update】スキル更新成功', [
                        'freelancer_id' => $freelancer->id,
                        'previous_skill_ids' => $currentSkillIds,
                        'synced_skill_ids' => $payload['skills'],
                        'synced_count' => count($payload['skills']),
                    ]);
                } catch (\Exception $e) {
                    Log::error('【FreelancerProfileService::update】スキル更新失敗', [
                        'freelancer_id' => $freelancer->id,
                        'skill_ids' => $payload['skills'],
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                    ]);
                    throw $e;
                }
            } else {
                Log::debug('【FreelancerProfileService::update】スキル更新スキップ', [
                    'freelancer_id' => $freelancer->id,
                    'has_skills_key' => array_key_exists('skills', $payload),
                    'skills_type' => isset($payload['skills']) ? gettype($payload['skills']) : null,
                    'current_skill_count' => $freelancer->skills()->count(),
                ]);
            }

            // custom_skillsが来たら置き換える（簡易実装：全削除→再作成）
            if (array_key_exists('custom_skills', $payload) && is_array($payload['custom_skills'])) {
                $currentCustomSkills = $freelancer->customSkills()->get()->pluck('name')->toArray();
                Log::debug('【FreelancerProfileService::update】カスタムスキル更新開始', [
                    'freelancer_id' => $freelancer->id,
                    'current_custom_skills' => $currentCustomSkills,
                    'current_custom_skills_count' => count($currentCustomSkills),
                    'new_custom_skills_raw_count' => count($payload['custom_skills']),
                    'new_custom_skills_raw' => $payload['custom_skills'],
                ]);

                try {
                    // 既存の自由入力スキルをいったん削除する
                    $deletedCount = $freelancer->customSkills()->delete();
                    Log::debug('【FreelancerProfileService::update】既存カスタムスキル削除', [
                        'freelancer_id' => $freelancer->id,
                        'deleted_count' => $deletedCount,
                    ]);

                    // 表示順を管理するカウンタ
                    $order = 1;
                    $createdCount = 0;
                    $skippedCount = 0;
                    // 入力されたスキル名を順番に処理する
                    foreach ($payload['custom_skills'] as $index => $skillName) {
                        // 空文字などは保存しない（入力ノイズを除去）
                        if (!is_string($skillName) || trim($skillName) === '') {
                            Log::debug('【FreelancerProfileService::update】カスタムスキルをスキップ（空文字または無効）', [
                                'freelancer_id' => $freelancer->id,
                                'index' => $index,
                                'skill_name_raw' => $skillName,
                                'skill_name_type' => gettype($skillName),
                            ]);
                            $skippedCount++;
                            continue;
                        }

                        try {
                            // 1件ずつ関連テーブルへ登録する
                            $customSkill = $freelancer->customSkills()->create([
                                // 表示用スキル名
                                'name' => trim($skillName),
                                // 画面表示順
                                'sort_order' => $order,
                            ]);

                            Log::debug('【FreelancerProfileService::update】カスタムスキル登録成功', [
                                'freelancer_id' => $freelancer->id,
                                'custom_skill_id' => $customSkill->id,
                                'skill_name' => trim($skillName),
                                'sort_order' => $order,
                                'index' => $index,
                            ]);

                            // 次の表示順へ進める
                            $order++;
                            $createdCount++;
                        } catch (\Exception $e) {
                            Log::error('【FreelancerProfileService::update】カスタムスキル登録失敗', [
                                'freelancer_id' => $freelancer->id,
                                'skill_name' => $skillName,
                                'sort_order' => $order,
                                'index' => $index,
                                'error_message' => $e->getMessage(),
                                'error_file' => $e->getFile(),
                                'error_line' => $e->getLine(),
                            ]);
                            throw $e;
                        }
                    }

                    Log::info('【FreelancerProfileService::update】カスタムスキル更新完了', [
                        'freelancer_id' => $freelancer->id,
                        'deleted_count' => $deletedCount,
                        'created_count' => $createdCount,
                        'skipped_count' => $skippedCount,
                        'total_processed' => count($payload['custom_skills']),
                    ]);
                } catch (\Exception $e) {
                    Log::error('【FreelancerProfileService::update】カスタムスキル更新処理でエラー発生', [
                        'freelancer_id' => $freelancer->id,
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                    ]);
                    throw $e;
                }
            } else {
                Log::debug('【FreelancerProfileService::update】カスタムスキル更新スキップ', [
                    'freelancer_id' => $freelancer->id,
                    'has_custom_skills_key' => array_key_exists('custom_skills', $payload),
                    'custom_skills_type' => isset($payload['custom_skills']) ? gettype($payload['custom_skills']) : null,
                    'current_custom_skills_count' => $freelancer->customSkills()->count(),
                ]);
            }

            // portfolio_urlsが来たら置き換える（簡易実装：全削除→再作成）
            if (array_key_exists('portfolio_urls', $payload) && is_array($payload['portfolio_urls'])) {
                $currentPortfolios = $freelancer->portfolios()->get()->pluck('url')->toArray();
                Log::debug('【FreelancerProfileService::update】ポートフォリオURL更新開始', [
                    'freelancer_id' => $freelancer->id,
                    'current_portfolios' => $currentPortfolios,
                    'current_portfolios_count' => count($currentPortfolios),
                    'new_portfolio_urls_raw_count' => count($payload['portfolio_urls']),
                    'new_portfolio_urls_raw' => $payload['portfolio_urls'],
                ]);

                try {
                    // 既存のポートフォリオURLをいったん削除する
                    $deletedCount = $freelancer->portfolios()->delete();
                    Log::debug('【FreelancerProfileService::update】既存ポートフォリオURL削除', [
                        'freelancer_id' => $freelancer->id,
                        'deleted_count' => $deletedCount,
                    ]);

                    // 表示順を管理するカウンタ
                    $order = 1;
                    $createdCount = 0;
                    $skippedCount = 0;
                    // URLを順番に処理する
                    foreach ($payload['portfolio_urls'] as $index => $url) {
                        // 空文字などは保存しない（入力ノイズを除去）
                        if (!is_string($url) || trim($url) === '') {
                            Log::debug('【FreelancerProfileService::update】ポートフォリオURLをスキップ（空文字または無効）', [
                                'freelancer_id' => $freelancer->id,
                                'index' => $index,
                                'url_raw' => $url,
                                'url_type' => gettype($url),
                            ]);
                            $skippedCount++;
                            continue;
                        }

                        try {
                            // 1件ずつ関連テーブルへ登録する
                            $portfolio = $freelancer->portfolios()->create([
                                // URL本体
                                'url' => trim($url),
                                // 画面表示順
                                'sort_order' => $order,
                            ]);

                            Log::debug('【FreelancerProfileService::update】ポートフォリオURL登録成功', [
                                'freelancer_id' => $freelancer->id,
                                'portfolio_id' => $portfolio->id,
                                'url' => trim($url),
                                'sort_order' => $order,
                                'index' => $index,
                            ]);

                            // 次の表示順へ進める
                            $order++;
                            $createdCount++;
                        } catch (\Exception $e) {
                            Log::error('【FreelancerProfileService::update】ポートフォリオURL登録失敗', [
                                'freelancer_id' => $freelancer->id,
                                'url' => $url,
                                'sort_order' => $order,
                                'index' => $index,
                                'error_message' => $e->getMessage(),
                                'error_file' => $e->getFile(),
                                'error_line' => $e->getLine(),
                            ]);
                            throw $e;
                        }
                    }

                    Log::info('【FreelancerProfileService::update】ポートフォリオURL更新完了', [
                        'freelancer_id' => $freelancer->id,
                        'deleted_count' => $deletedCount,
                        'created_count' => $createdCount,
                        'skipped_count' => $skippedCount,
                        'total_processed' => count($payload['portfolio_urls']),
                    ]);
                } catch (\Exception $e) {
                    Log::error('【FreelancerProfileService::update】ポートフォリオURL更新処理でエラー発生', [
                        'freelancer_id' => $freelancer->id,
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                    ]);
                    throw $e;
                }
            } else {
                Log::debug('【FreelancerProfileService::update】ポートフォリオURL更新スキップ', [
                    'freelancer_id' => $freelancer->id,
                    'has_portfolio_urls_key' => array_key_exists('portfolio_urls', $payload),
                    'portfolio_urls_type' => isset($payload['portfolio_urls']) ? gettype($payload['portfolio_urls']) : null,
                    'current_portfolios_count' => $freelancer->portfolios()->count(),
                ]);
            }

            // 更新済みのプロフィールを返す
            Log::info('【FreelancerProfileService::update】プロフィール更新処理完了', [
                'freelancer_id' => $freelancer->id,
                'user_id' => $freelancer->user_id,
                'display_name' => $freelancer->display_name,
                'has_icon' => !empty($freelancer->icon_path),
                'icon_path' => $freelancer->icon_path,
                'skills_count' => $freelancer->skills()->count(),
                'custom_skills_count' => $freelancer->customSkills()->count(),
                'portfolios_count' => $freelancer->portfolios()->count(),
                'updated_at' => $freelancer->updated_at,
            ]);

            Log::debug('【FreelancerProfileService::update】トランザクションコミット', [
                'freelancer_id' => $freelancer->id,
            ]);

            return $freelancer;
            });
        } catch (\Exception $e) {
            Log::error('【FreelancerProfileService::update】プロフィール更新処理でエラー発生', [
                'freelancer_id' => $freelancer->id,
                'user_id' => $freelancer->user_id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'payload_keys' => array_keys($payload),
            ]);
            throw $e;
        }
    }
}

