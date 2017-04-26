<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCoachingCall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
			$table->dropColumn('course_id');
        });

		Schema::table('sessions', function (Blueprint $table) {
			$table->string('type')->after('bucket_url');
			$table->integer('course_id')->unsigned()->after('type');
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
			// $table->integer('course_id')->unsigned()->nullable();
			// $table->foreign('course_id')->references('id')->on('courses');
        });

		Schema::table('sessions', function (Blueprint $table) {
			$table->dropColumn('type');
			$table->dropColumn('course_id');
		});
    }
}
