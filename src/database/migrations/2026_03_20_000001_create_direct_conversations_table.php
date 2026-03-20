<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectConversationsTable extends Migration
{
    public function up()
    {
        // 途中失敗後の再実行で「既にテーブルがある」エラーを避ける
        if (Schema::hasTable('direct_conversations')) {
            return;
        }

        Schema::create('direct_conversations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('freelancer_id')
                ->constrained('freelancers')
                ->cascadeOnDelete();

            $table->enum('latest_sender_type', ['company', 'freelancer'])->nullable();
            $table->unsignedBigInteger('latest_sender_id')->nullable();
            $table->timestamp('latest_message_at')->nullable();
            $table->boolean('is_unread_for_company')->default(false);
            $table->boolean('is_unread_for_freelancer')->default(false);

            // MySQL の識別子（インデックス名）上限が厳しいため、短い名前を明示する
            $table->unique(['company_id', 'freelancer_id'], 'dm_cf_uidx');
            $table->index(['company_id', 'is_unread_for_company', 'latest_message_at'], 'dm_c_unread_lat_idx');
            $table->index(['freelancer_id', 'is_unread_for_freelancer', 'latest_message_at'], 'dm_f_unread_lat_idx');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('direct_conversations');
    }
}
