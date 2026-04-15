<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BuyerProfileService
{
    public function register(User $user, array $payload): Buyer
    {
        return DB::transaction(function () use ($user, $payload): Buyer {
            if ($user->buyer()->exists()) {
                return $user->buyer()->firstOrFail();
            }

            $iconPath = $payload['icon_path'] ?? null;
            if (isset($payload['icon']) && $payload['icon'] instanceof UploadedFile) {
                $iconPath = $payload['icon']->store('buyer_icons', 'public');
            }

            $buyer = Buyer::create([
                'user_id' => $user->id,
                'display_name' => $payload['display_name'],
                'icon_path' => $iconPath,
                'age_group' => $payload['age_group'] ?? null,
                'prefecture' => $payload['prefecture'] ?? null,
                'address' => $payload['address'] ?? null,
            ]);

            Log::info('[BuyerProfileService] register 完了', [
                'user_id' => $user->id,
                'buyer_id' => $buyer->id,
            ]);

            return $buyer;
        });
    }

    public function update(Buyer $buyer, array $payload): Buyer
    {
        return DB::transaction(function () use ($buyer, $payload): Buyer {
            $iconPath = null;
            if (isset($payload['icon']) && $payload['icon'] instanceof UploadedFile) {
                // 既存アイコンがあれば削除（存在しない場合は失敗しても続行）
                if (!empty($buyer->icon_path)) {
                    try {
                        Storage::disk('public')->delete($buyer->icon_path);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                $iconPath = $payload['icon']->store('buyer_icons', 'public');
            }

            $buyer->fill([
                'display_name' => $payload['display_name'] ?? $buyer->display_name,
                'icon_path' => array_key_exists('icon_path', $payload)
                    ? $payload['icon_path']
                    : ($iconPath !== null ? $iconPath : $buyer->icon_path),
                'age_group' => $payload['age_group'] ?? $buyer->age_group,
                'prefecture' => $payload['prefecture'] ?? $buyer->prefecture,
                'address' => $payload['address'] ?? $buyer->address,
            ]);

            $buyer->save();

            return $buyer->refresh();
        });
    }
}

