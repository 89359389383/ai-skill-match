<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBuyerToUsersRoleEnum extends Migration
{
    public function up(): void
    {
        // MySQL: ENUM を MODIFY するには DB::statement が無難です。
        // 目的: production で role='buyer' が Data truncated になるのを解消。
        DB::statement("ALTER TABLE users MODIFY role ENUM('company','freelancer','buyer') NOT NULL");
    }

    public function down(): void
    {
        // ロールバック時は元に戻すが、運用上必要になったら別途調整してください。
        DB::statement("ALTER TABLE users MODIFY role ENUM('company','freelancer') NOT NULL");
    }
}

