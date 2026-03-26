<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skill_listings', function (Blueprint $table) {
            $table->text('purchase_instructions')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('skill_listings', function (Blueprint $table) {
            $table->dropColumn('purchase_instructions');
        });
    }
};

