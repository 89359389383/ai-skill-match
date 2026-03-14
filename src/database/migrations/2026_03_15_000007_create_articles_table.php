<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            // ID
            $table->id();

            // 投稿者（共通ユーザー）
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // タイトル
            $table->string('title');

            // 概要
            $table->string('excerpt', 200);

            // カテゴリー（現状は固定文字列。将来はマスタ化）
            $table->string('category');

            // アイキャッチ画像URL
            $table->string('eyecatch_image_url')->nullable();

            // 記事構造（大項目/中項目）をJSONで保持
            $table->json('structure')->nullable();

            // ステータス：0:下書き / 1:公開中 / 2:非公開
            $table->unsignedTinyInteger('status')->default(1);

            // 公開日時
            $table->timestamp('published_at')->nullable();

            // 集計（一覧ソート用）
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);

            // 作成日時・更新日時
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['category']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}

