<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLockDateToDateTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dateTime('lock_date')->nullable()->change();
        });

        Schema::table('modules', function (Blueprint $table) {
			$table->dateTime('lock_date')->nullable()->change();
		});

		Schema::table('lessons', function (Blueprint $table) {
			$table->dateTime('lock_date')->nullable()->change();
		});

		Schema::table('sessions', function (Blueprint $table) {
			$table->dateTime('lock_date')->nullable()->change();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->date('lock_date')->nullable()->change();
        });

		Schema::table('modules', function (Blueprint $table) {
			$table->date('lock_date')->nullable()->change();
		});

		Schema::table('lessons', function (Blueprint $table) {
			$table->date('lock_date')->nullable()->change();
		});

		Schema::table('sessions', function (Blueprint $table) {
			$table->date('lock_date')->nullable()->change();
		});
    }
}
