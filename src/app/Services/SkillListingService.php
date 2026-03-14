<?php

namespace App\Services;

use App\Models\Freelancer;
use App\Models\SkillListing;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SkillListingService
{
    /**
     * スキル出品を作成する（本体 + 付随データをまとめて登録）。
     *
     * ここでやる理由:
     * - 「出品本体」「スキル紐付け」「添付（画像/ファイル）」は別テーブル
     * - 途中で失敗すると不整合が起きるので transaction でまとめたい
     * - Controller は“受付/入力チェック/画面遷移”に寄せ、DB更新は Service に寄せる
     */
    public function store(Freelancer $freelancer, array $data): SkillListing
    {
        // まずは「必須の前提」が崩れていないかをチェックしておく
        // （Controller でも validate するが、二重防御しておくと安心）
        if (!isset($data['title']) || trim((string) $data['title']) === '') {
            throw ValidationException::withMessages(['title' => 'タイトルは必須です']);
        }

        return DB::transaction(function () use ($freelancer, $data): SkillListing {
            // 1) 出品本体を作る
            $listing = SkillListing::create([
                'freelancer_id' => $freelancer->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'price' => (int) ($data['price'] ?? 0),
                'pricing_type' => $data['pricing_type'] ?? 'fixed',
                'thumbnail_url' => $data['thumbnail_url'] ?? null,
                // 初期は「下書き」でも良いが、まずは公開導線を優先して1にする場合もある
                // 今回は “仕様が固まるまで安全側” に寄せ、下書き(0)で作成する
                'status' => 0,
                'delivery_days' => $data['delivery_days'] ?? null,
            ]);

            // 2) 共通スキルを紐付ける（複数可）
            // skill_ids が空なら紐付けはしない（出品自体は作れる）
            $skillIds = $data['skill_ids'] ?? [];
            if (is_array($skillIds) && count($skillIds) > 0) {
                $listing->skills()->sync($skillIds);
            }

            // 3) 添付（画像/ファイル）を登録する
            // ここでは “URLだけ” を受け取る簡易実装としておき、将来アップロードに差し替える
            $assets = $data['assets'] ?? [];
            if (is_array($assets)) {
                foreach ($assets as $i => $asset) {
                    // asset には type/url を期待する
                    if (!is_array($asset)) {
                        continue;
                    }
                    $listing->assets()->create([
                        'type' => $asset['type'] ?? 'image',
                        'url' => $asset['url'] ?? '',
                        'sort_order' => $asset['sort_order'] ?? $i,
                    ]);
                }
            }

            return $listing;
        });
    }
}

