<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('direct_conversation_message_attachments')) {
            return;
        }

        Schema::create('direct_conversation_message_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('direct_conversation_message_id');
            // MySQL の識別子長さ制限を避けるため、外部キー制約名を短く明示する
            $table->foreign('direct_conversation_message_id', 'dm_msg_att_fk')
                ->references('id')
                ->on('direct_conversation_messages')
                ->cascadeOnDelete();

            $table->string('attachment_name')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();

            $table->timestamps();

            $table->index(['direct_conversation_message_id'], 'dm_msg_attach_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_conversation_message_attachments');
    }
};

