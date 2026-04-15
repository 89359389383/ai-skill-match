<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExtendDirectConversationsForBuyer extends Migration
{
    public function up(): void
    {
        // buyer_id (nullable)
        if (!Schema::hasColumn('direct_conversations', 'buyer_id')) {
            Schema::table('direct_conversations', function (Blueprint $table) {
                $table->unsignedBigInteger('buyer_id')->nullable()->after('freelancer_id');
            });
        }

        // unread flag for buyer
        if (!Schema::hasColumn('direct_conversations', 'is_unread_for_buyer')) {
            Schema::table('direct_conversations', function (Blueprint $table) {
                $table->boolean('is_unread_for_buyer')->default(false)->after('is_unread_for_freelancer');
            });
        }

        // FK to buyers (id)
        // ※同名FKが存在する場合の安全策が必要になるため、ここでは try/catch で許容します
        try {
            Schema::table('direct_conversations', function (Blueprint $table) {
                if (!array_key_exists('buyer_id', $table->getColumnListing())) {
                    return;
                }
                // foreignId でなく foreign() を使うことで既存状態により起きる差異を減らします
                $table->foreign('buyer_id', 'direct_conversations_buyer_id_foreign')
                    ->references('id')
                    ->on('buyers')
                    ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // 既に存在する場合などは無視
        }

        // latest_sender_type / initiator_type enum を拡張
        DB::statement("ALTER TABLE direct_conversations MODIFY latest_sender_type ENUM('company','freelancer','buyer') NULL");
        DB::statement("ALTER TABLE direct_conversations MODIFY initiator_type ENUM('company','freelancer','buyer') NULL");

        // buyer専用ユニーク（company/freelancer側の既存挙動は維持）
        // buyer-freelancer 会話の重複を防ぐ
        DB::statement("
            CREATE UNIQUE INDEX dm_bf_uidx
            ON direct_conversations (buyer_id, freelancer_id)
        ");

        // インデックス（unreadと最新メッセージ検索の効率化）
        DB::statement("CREATE INDEX direct_conversations_buyer_unread_idx ON direct_conversations (buyer_id, is_unread_for_buyer)");
        DB::statement("CREATE INDEX direct_conversations_buyer_latest_idx ON direct_conversations (buyer_id, latest_message_at)");
    }

    public function down(): void
    {
        // down は厳密には保証しません（環境依存差分が出やすいため）
        DB::statement("ALTER TABLE direct_conversations MODIFY latest_sender_type ENUM('company','freelancer') NULL");
        DB::statement("ALTER TABLE direct_conversations MODIFY initiator_type ENUM('company','freelancer') NULL");
    }
}

