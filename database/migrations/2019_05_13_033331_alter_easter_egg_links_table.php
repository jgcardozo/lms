<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEasterEggLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('easter_egg_links', function (Blueprint $table) {
            $table->dropForeign(['lesson_id']);
            $table->dropColumn('lesson_id');
            $table->string('linkable_type')->after('id');
            $table->unsignedInteger('linkable_id')->after('linkable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('easter_egg_links', function (Blueprint $table) {
            $table->unsignedInteger('lesson_id')->after('id');
            $table->dropMorphs('linkable');
        });
    }
}
