<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('message_attachments')) {
            return;
        }

        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();

            // thread messages（messages）に紐づく添付
            $table->foreignId('message_id')
                ->constrained('messages')
                ->cascadeOnDelete();

            // 添付メタ情報
            $table->string('attachment_name')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};

