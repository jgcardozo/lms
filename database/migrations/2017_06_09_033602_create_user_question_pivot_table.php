<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserQuestionPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('lesson_id')->unsigned()->index();
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');

            $table->integer('question_id')->unsigned()->index();
            $table->foreign('question_id')->references('id')->on('courses')->onDelete('cascade');

            $table->timestamp('created_at');

            $table->primary(['user_id', 'lesson_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_user');
    }
}
