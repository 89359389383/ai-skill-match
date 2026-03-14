<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillListingAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_listing_assets', function (Blueprint $table) {
            // ID
            $table->id();

            // 出品スキル
            $table->foreignId('skill_listing_id')
                ->constrained('skill_listings')
                ->cascadeOnDelete();

            // asset種別：image / file
            $table->enum('type', ['image', 'file'])->default('image');

            // URL or パス
            $table->string('url');

            // 並び順
            $table->unsignedSmallInteger('sort_order')->default(0);

            // 作成日時・更新日時
            $table->timestamps();

            $table->index(['skill_listing_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_listing_assets');
    }
}

