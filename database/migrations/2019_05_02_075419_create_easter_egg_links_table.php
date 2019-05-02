<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEasterEggLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('easter_egg_links', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('cohort_id');
            $table->string('fb_link');
            $table->timestamps();

            $table->foreign('lesson_id')
                ->references('id')->on('lessons')
                ->onDelete('cascade');

            $table->foreign('cohort_id')
                ->references('id')->on('cohorts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('easter_egg_links');
    }
}
