<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceResourceTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_resource_tag', function (Blueprint $table) {
            $table->integer('resource_id')->unsigned();
            $table->integer('resource_tag_id')->unsigned();

            $table->unique(['resource_id', 'resource_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('resource_resource_tag');
    }
}
