<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamificationStreakLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_streak_logs', function (Blueprint $table) {
			$table->increments('id');
            $table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users');

			$table->integer('count');

			$table->string('type');

			$table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('g_streak_logs');
    }
}
