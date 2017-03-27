<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources', function (Blueprint $table) {
			$table->dropForeign(['session_id']);
            $table->dropColumn(['session_id']);
			$table->bigInteger('file_size');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->integer('session_id')->unsigned();
            $table->foreign('session_id')->references('id')->on('sessions');
        });
    }
}
