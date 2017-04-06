<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIsCourseProductIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('is_course_products', function (Blueprint $table) {
			$table->integer('course_id')->unsigned()->index();
			$table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');

			$table->integer('product_id');

			$table->primary(['course_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('is_course_products');
    }
}
