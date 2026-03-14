<?php

namespace App\Services;

use App\Models\SkillListing;
use App\Models\User;

class SkillInquiryService
{
    /**
     * 問い合わせ送信（将来拡張用）。
     *
     * 現状:
     * - 問い合わせ用のテーブル/チャット連携が未確定のため “保存はしない”
     * - Controller から呼ばれても破綻しないように、入り口だけ用意しておく
     *
     * 将来:
     * - skill_inquiries テーブルを作る、または threads/messages に流す、などの仕様が決まったら実装する
     */
    public function store(User $user, SkillListing $listing, string $message): void
    {
        // ここでは何もしない（将来実装）
        // - ただし、呼び出し側が「成功として扱える」ように例外を投げない。
        //
        // NOTE:
        // - 変数は引数として受け取るだけでOK（未使用でもPHPの構文エラーにはならない）
        // - 実装が決まったら、ここに保存処理を追加する
    }
}

