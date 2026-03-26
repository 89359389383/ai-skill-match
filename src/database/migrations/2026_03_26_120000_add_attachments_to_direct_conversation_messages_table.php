<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('direct_conversation_messages')) {
            return;
        }

        Schema::table('direct_conversation_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('direct_conversation_messages', 'attachment_name')) {
                $table->string('attachment_name')->nullable()->after('body');
            }
            if (!Schema::hasColumn('direct_conversation_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('attachment_name');
            }
            if (!Schema::hasColumn('direct_conversation_messages', 'attachment_mime')) {
                $table->string('attachment_mime', 120)->nullable()->after('attachment_path');
            }
            if (!Schema::hasColumn('direct_conversation_messages', 'attachment_size')) {
                $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_mime');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('direct_conversation_messages')) {
            return;
        }

        Schema::table('direct_conversation_messages', function (Blueprint $table) {
            $columns = [
                'attachment_name',
                'attachment_path',
                'attachment_mime',
                'attachment_size',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('direct_conversation_messages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
