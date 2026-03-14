<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobDetailsColumnsToJobsTable extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
            $table->text('desired_persona')->nullable()->after('description');
            $table->date('work_start_date')->nullable()->after('work_time_text');
            $table->date('publish_end_date')->nullable()->after('work_start_date');
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'desired_persona', 'work_start_date', 'publish_end_date']);
        });
    }
}

