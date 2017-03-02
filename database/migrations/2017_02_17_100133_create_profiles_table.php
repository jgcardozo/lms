<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('phone1');
            $table->string('phone2');
            $table->string('company')->default('');
            $table->string('address')->default('');
            $table->string('city')->default('');
            $table->string('country')->default('');
            $table->string('state')->default('');
            $table->string('zip')->default('');

            $table->timestamps();

            $table->primary('user_id');
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
