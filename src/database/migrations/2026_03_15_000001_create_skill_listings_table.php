<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_listings', function (Blueprint $table) {
            // ID
            $table->id();

            // 出品者（フリーランス）
            $table->foreignId('freelancer_id')
                ->constrained('freelancers')
                ->cascadeOnDelete();

            // タイトル
            $table->string('title');

            // 説明
            $table->text('description');

            // 価格
            $table->unsignedInteger('price');

            // 価格種別：fixed（固定）/ hourly（時間単位）
            $table->enum('pricing_type', ['fixed', 'hourly'])->default('fixed');

            // サムネイルURL（将来的にアップロードへ差し替え）
            $table->string('thumbnail_url')->nullable();

            // ステータス：0:下書き / 1:公開中 / 2:停止中
            $table->unsignedTinyInteger('status')->default(0);

            // 目安納期（日）
            $table->unsignedSmallInteger('delivery_days')->nullable();

            // レビュー集計（将来の高速化用）
            $table->unsignedSmallInteger('reviews_count')->default(0);
            $table->decimal('rating_average', 2, 1)->default(0);

            // 作成日時・更新日時
            $table->timestamps();

            $table->index(['status', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_listings');
    }
}

