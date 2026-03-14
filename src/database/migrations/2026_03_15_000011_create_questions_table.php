<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            // ID
            $table->id();

            // 投稿者（共通ユーザー）
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // タイトル
            $table->string('title');

            // 本文
            $table->text('content');

            // カテゴリー（現状は固定文字列。将来はマスタ化）
            $table->string('category')->default('すべて');

            // 解決済み
            $table->boolean('is_resolved')->default(false);

            // AI参考回答（将来用）
            $table->text('ai_answer')->nullable();

            // 集計（一覧表示用）
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('answers_count')->default(0);

            // ベストアンサー（answers.id）
            $table->unsignedBigInteger('accepted_answer_id')->nullable();

            // 作成日時・更新日時
            $table->timestamps();

            $table->index(['is_resolved', 'created_at']);
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
        Schema::dropIfExists('questions');
    }
}

