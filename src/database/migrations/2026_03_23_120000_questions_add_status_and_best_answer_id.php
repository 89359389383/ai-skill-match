<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuestionsAddStatusAndBestAnswerId extends Migration
{
    /**
     * is_resolved / accepted_answer_id を status / best_answer_id に置き換える。
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('status', 20)->default('open')->after('category');
            $table->unsignedBigInteger('best_answer_id')->nullable()->after('answers_count');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE questions SET status = IF(is_resolved = 1, 'resolved', 'open')");
        } else {
            DB::table('questions')->where('is_resolved', true)->update(['status' => 'resolved']);
            DB::table('questions')->where('is_resolved', false)->update(['status' => 'open']);
        }

        DB::statement('UPDATE questions SET best_answer_id = accepted_answer_id WHERE accepted_answer_id IS NOT NULL');

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['is_resolved', 'created_at']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['is_resolved', 'accepted_answer_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('is_resolved')->default(false)->after('category');
            $table->unsignedBigInteger('accepted_answer_id')->nullable()->after('answers_count');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE questions SET is_resolved = IF(status = 'resolved', 1, 0)");
        } else {
            DB::table('questions')->where('status', 'resolved')->update(['is_resolved' => true]);
            DB::table('questions')->where('status', '!=', 'resolved')->update(['is_resolved' => false]);
        }

        DB::statement('UPDATE questions SET accepted_answer_id = best_answer_id WHERE best_answer_id IS NOT NULL');

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['status', 'best_answer_id']);
            $table->index(['is_resolved', 'created_at']);
        });
    }
}
