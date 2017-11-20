<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCoursesTableAddFreeWatchField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('must_watch')->default(true)->after('billing_is_products');
            $table->boolean('complete_feature')->default(true)->after('must_watch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table)
        {
            $table->dropColumn('must_watch');
            $table->dropColumn('complete_feature');
        });
    }
}
