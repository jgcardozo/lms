<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcechildResourcebankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resourcechild_resourcebank', function (Blueprint $table) {
            $table->integer('child_id')->unsigned()->index();
            $table->foreign('child_id')->references('id')->on('resources_children')->onDelete('cascade');
            $table->integer('bank_id')->unsigned()->index();
            $table->foreign('bank_id')->references('id')->on('resources_banks')->onDelete('cascade');
            $table->primary(['child_id', 'bank_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resourcechild_resourcebank');
    }
}
