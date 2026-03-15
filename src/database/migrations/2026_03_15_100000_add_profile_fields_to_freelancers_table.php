<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * プロフィール詳細表示用の不足項目を追加
     */
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            $table->string('services_offered', 500)->nullable();
            $table->string('industry_specialties', 500)->nullable();
            $table->string('prefecture', 50)->nullable();
            $table->text('certifications')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('line_id', 100)->nullable();
            $table->string('twitter_url', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            $table->dropColumn([
                'services_offered',
                'industry_specialties',
                'prefecture',
                'certifications',
                'phone',
                'line_id',
                'twitter_url',
            ]);
        });
    }
};
