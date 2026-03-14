<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            // ID
            $table->id();

            // 質問
            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();

            // 投稿者（共通ユーザー）
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 回答本文
            $table->text('content');

            // ベストアンサー
            $table->boolean('is_accepted')->default(false);

            // リアクション集計
            $table->unsignedInteger('reactions_naruhodo')->default(0);
            $table->unsignedInteger('reactions_soudane')->default(0);
            $table->unsignedInteger('reactions_arigatou')->default(0);
            $table->unsignedInteger('likes_count')->default(0);

            // 作成日時・更新日時
            $table->timestamps();

            $table->index(['question_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}

