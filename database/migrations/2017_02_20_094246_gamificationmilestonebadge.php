<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Gamificationmilestonebadge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_milestone_badges', function (Blueprint $table) {
            $table->integer('milestone_id')->unsigned()->index();
            $table->foreign('milestone_id')->references('id')->on('g_milestones')->onDelete('cascade');
            $table->integer('badge_id')->unsigned()->index();
            $table->foreign('badge_id')->references('id')->on('g_badges')->onDelete('cascade');
            $table->primary(['milestone_id', 'badge_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('g_milestone_badges');
    }
}
