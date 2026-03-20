<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectConversationMessagesTable extends Migration
{
    public function up()
    {
        // 過去の migrate が途中失敗した場合など、テーブルだけ残って migrations に未記録だと再実行で衝突するためスキップする
        if (Schema::hasTable('direct_conversation_messages')) {
            return;
        }

        Schema::create('direct_conversation_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('direct_conversation_id')
                ->constrained('direct_conversations')
                ->cascadeOnDelete();

            $table->enum('sender_type', ['company', 'freelancer']);
            $table->unsignedBigInteger('sender_id');
            $table->text('body');
            $table->timestamp('sent_at');
            $table->softDeletes();
            $table->timestamps();

            // MySQL の識別子（インデックス名）上限に引っかからないよう短い名前を明示
            $table->index(['direct_conversation_id', 'sent_at'], 'dm_msg_dc_sent_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('direct_conversation_messages');
    }
}
