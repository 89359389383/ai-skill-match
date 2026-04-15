<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ExtendDirectConversationMessagesForBuyer extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE direct_conversation_messages MODIFY sender_type ENUM('company','freelancer','buyer') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE direct_conversation_messages MODIFY sender_type ENUM('company','freelancer') NOT NULL");
    }
}

