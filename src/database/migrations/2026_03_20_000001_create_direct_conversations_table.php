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

            // company_id: nullable（フリーランス→フリーランスの場合はNULL）
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            // freelancer_id: nullable（企業→企業の場合はNULL）
            $table->unsignedBigInteger('freelancer_id')->nullable();
            $table->foreign('freelancer_id')
                ->references('id')
                ->on('freelancers')
                ->onDelete('cascade');

            $table->enum('latest_sender_type', ['company', 'freelancer'])->nullable();
            $table->unsignedBigInteger('latest_sender_id')->nullable();
            $table->timestamp('latest_message_at')->nullable();
            $table->boolean('is_unread_for_company')->default(false);
            $table->boolean('is_unread_for_freelancer')->default(false);

            // 同じrole同士の会話で、会話を開始した人を追跡するためのカラム
            $table->enum('initiator_type', ['company', 'freelancer'])->nullable();
            $table->unsignedBigInteger('initiator_id')->nullable();

            // MySQL の識別子（インデックス名）上限が厳しいため、短い名前を明示する
            // nullableなカラムを含む一意制約（NULL値は複数許容される）
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
