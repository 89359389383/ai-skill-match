<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_reviews', function (Blueprint $table) {
            // ID
            $table->id();

            // 対象スキル（出品）
            $table->foreignId('skill_listing_id')
                ->constrained('skill_listings')
                ->cascadeOnDelete();

            // 投稿者（共通ユーザー）
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 評価（1-5想定）
            $table->unsignedTinyInteger('rating');

            // 本文
            $table->text('body')->nullable();

            // 作成日時・更新日時
            $table->timestamps();

            $table->index(['skill_listing_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_reviews');
    }
}

