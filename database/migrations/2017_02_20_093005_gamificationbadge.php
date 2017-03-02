<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Gamificationbadge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_badges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias', 32)->unique();
            $table->string('title');
            $table->boolean('repeatable')->default(0);

            $table->integer('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('g_events');

            $table->string('description');
            $table->string('notification');
            $table->integer('points')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('g_badges');
    }
}
