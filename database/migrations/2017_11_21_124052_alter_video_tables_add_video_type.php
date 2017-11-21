<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVideoTablesAddVideoType extends Migration
{
    protected $tables = [
        'courses',
        'modules',
        'lessons',
        'sessions',
        'lesson_questions'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach($this->tables as $table)
        {
            Schema::table($table, function (Blueprint $table) {
                $table->integer('video_type_id')->unsigned()->default(1)->after('video_url');
            });
        }

        Schema::table('lessons', function (Blueprint $table) {
            $table->integer('bonus_video_type_id')->unsigned()->default(1)->after('video_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach($this->tables as $table)
        {
            Schema::table($table, function (Blueprint $table)
            {
                $table->dropColumn('video_type_id');
            });
        }

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('bonus_video_type_id');
        });
    }
}
