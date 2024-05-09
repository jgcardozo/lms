<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->timestamp('video_reveal_at')->after('course_id')->nullable(); //'video_url'
            $table->timestamp('learnmore_reveal_at')->after('course_id')->nullable(); //'learn_more'
            $table->integer('schedule_id')->after('course_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('video_reveal_at');
            $table->dropColumn('learnmore_reveal_at');
            $table->dropColumn('schedule_id');
        });
    }
}
