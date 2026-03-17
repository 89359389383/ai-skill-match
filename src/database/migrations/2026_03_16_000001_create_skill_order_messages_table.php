<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillOrderMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_order_messages', function (Blueprint $table) {
            // ID
            $table->id();

            // 取引（注文）ID
            $table->foreignId('skill_order_id')
                ->constrained('skill_orders')
                ->cascadeOnDelete();

            // 送信者（共通ユーザー）
            // - system メッセージの場合は null（sender_user_id を持たない）
            $table->foreignId('sender_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 種別：text / file / system
            $table->enum('message_type', ['text', 'file', 'system'])->default('text');

            // 本文（file の場合は任意、system は本文必須想定）
            $table->text('body')->nullable();

            // ファイル情報（file の場合のみ）
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();

            // 送信日時
            $table->timestamp('sent_at');

            // 作成日時・更新日時
            $table->timestamps();

            $table->index(['skill_order_id', 'sent_at']);
            $table->index(['sender_user_id', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_order_messages');
    }
}

