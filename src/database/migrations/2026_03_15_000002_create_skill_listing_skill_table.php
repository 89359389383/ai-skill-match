<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillListingSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_listing_skill', function (Blueprint $table) {
            // ID
            $table->id();

            // 出品スキル
            $table->foreignId('skill_listing_id')
                ->constrained('skill_listings')
                ->cascadeOnDelete();

            // 共通スキル（既存 skills）
            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            // 作成日時・更新日時
            $table->timestamps();

            $table->unique(['skill_listing_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_listing_skill');
    }
}

