<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources_banks', function (Blueprint $table) {
            // 'title', 'slug', 'description', 'content', 'video_url', 'video_type_id', 'featured_image', 'header_image', 'sidebar_content'
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->longText('description');
			$table->integer('lft')->default(0);
			$table->longText('header_image')->nullable();
            $table->longText('featured_image')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('video_type_id')->unsigned()->default(1);
			$table->longText('sidebar_content');
            $table->longText('content');
            $table->boolean('published')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resources_banks');
    }
}
