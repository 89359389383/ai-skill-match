<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyProfileService
{
    /**
     * 企業プロフィールを登録する
     *
     * 設計根拠（CompanyProfileService 詳細設計）
     * - Controllerは入口に徹し、保存ロジックはServiceへ集約する
     * - 将来的な拡張（追加テーブル更新など）に備え、トランザクションでまとめる
     */
    public function register(User $user, array $payload): Company
    {
        // 今はcompaniesだけだが、将来の複数更新に備えてトランザクションでまとめる
        return DB::transaction(function () use ($user, $payload): Company {
            $payloadKeys = array_keys($payload);
            if (isset($payload['icon'])) {
                $payloadKeys = array_values(array_diff($payloadKeys, ['icon']));
            }

            Log::info('[企業登録] CompanyProfileService::register トランザクション内', [
                'user_id' => $user->id,
                'payload_field_keys' => $payloadKeys,
                'has_uploaded_icon' => isset($payload['icon']) && $payload['icon'] instanceof UploadedFile,
                'has_icon_path' => !empty($payload['icon_path']),
            ]);

            // 既に企業プロフィールがある場合は二重登録を防ぐ（Controllerでも防ぐが二重防御）
            if ($user->company()->exists()) {
                $existing = $user->company()->firstOrFail();
                Log::warning('[企業登録] CompanyProfileService::register 既存企業あり（既存レコードを返却）', [
                    'user_id' => $user->id,
                    'company_id' => $existing->id,
                ]);
                // ここでは「既存を返す」ことで、アプリを落とさず安全側に倒す
                return $existing;
            }

            // アイコン（icon）を保存して icon_path を保持する
            $iconPath = $payload['icon_path'] ?? null;
            if (!empty($iconPath)) {
                Log::info('[企業登録] CompanyProfileService::register 一時保存済みアイコンを利用', [
                    'user_id' => $user->id,
                    'stored_icon_path' => $iconPath,
                ]);
            }
            if (isset($payload['icon']) && $payload['icon'] instanceof UploadedFile) {
                $iconPath = $payload['icon']->store('company_icons', 'public');
                Log::info('[企業登録] CompanyProfileService::register アイコン保存', [
                    'user_id' => $user->id,
                    'stored_icon_path' => $iconPath,
                ]);
            } else {
                Log::warning('[企業登録] CompanyProfileService::register アイコンなし（想定外の場合は要調査）', [
                    'user_id' => $user->id,
                ]);
            }

            // companies テーブルへINSERTする（設計：企業プロフィール作成）
            $company = Company::create([
                // 紐づくユーザー
                'user_id' => $user->id,
                // 企業名（payloadはcompany_nameで来る想定）
                'name' => $payload['company_name'],
                // 会社概要（任意）
                'overview' => $payload['overview'] ?? null,
                // 担当者名（必須・FormRequestで検証）
                'contact_name' => $payload['contact_name'],
                // 部署（任意）
                'department' => $payload['department'] ?? null,
                // 自己紹介（任意）
                'introduction' => $payload['introduction'] ?? null,
                'icon_path' => $iconPath,
            ]);

            Log::info('[企業登録] CompanyProfileService::register companies INSERT 完了', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'name' => $company->name,
                'icon_path' => $company->icon_path,
                'contact_name_length' => mb_strlen((string) $company->contact_name),
            ]);

            return $company;
        });
    }

    /**
     * 企業プロフィールを更新する
     *
     * 設計方針:
     * - アイコンがアップロードされている場合のみ icon_path を差し替える
     * - それ以外の通常項目は fill で更新する
     */
    public function update(Company $company, array $payload): Company
    {
        return DB::transaction(function () use ($company, $payload): Company {
            Log::info('[企業登録/設定] CompanyProfileService::update 開始', [
                'company_id' => $company->id,
                'user_id' => $company->user_id,
                'payload_keys' => array_keys(array_diff_key($payload, ['icon' => true])),
                'has_new_icon' => isset($payload['icon']) && $payload['icon'] instanceof UploadedFile,
            ]);

            $iconPath = null;

            if (isset($payload['icon']) && $payload['icon'] instanceof UploadedFile) {
                $iconPath = $payload['icon']->store('company_icons', 'public');
                Log::info('[企業登録/設定] CompanyProfileService::update 新アイコン保存', [
                    'company_id' => $company->id,
                    'stored_icon_path' => $iconPath,
                ]);
            }

            $company->fill([
                'name' => $payload['name'] ?? $company->name,
                'overview' => $payload['overview'] ?? $company->overview,
                'contact_name' => $payload['contact_name'],
                'department' => $payload['department'] ?? $company->department,
                'introduction' => $payload['introduction'] ?? $company->introduction,
                'icon_path' => array_key_exists('icon_path', $payload)
                    ? $payload['icon_path']
                    : ($iconPath !== null ? $iconPath : $company->icon_path),
            ]);

            $company->save();
            $fresh = $company->refresh();

            Log::info('[企業登録/設定] CompanyProfileService::update 完了', [
                'company_id' => $fresh->id,
                'icon_path' => $fresh->icon_path,
            ]);

            return $fresh;
        });
    }
}

