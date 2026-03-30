<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            // 稼働ステータス（◎/〇/△/×）
            $table->string('work_availability_status', 30)->default('available_full');
        });

        // 既存データは念のため null を ◎ に補完
        DB::table('freelancers')
            ->whereNull('work_availability_status')
            ->update(['work_availability_status' => 'available_full']);
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            $table->dropColumn('work_availability_status');
        });
    }
};

