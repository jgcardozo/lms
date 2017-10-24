<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClassMarkerResultsAddPassedAtField extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('class_marker_results', function (Blueprint $table)
        {
            $table->timestamp('passed_at')
                  ->nullable()
                  ->after('cert_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_marker_results', function (Blueprint $table)
        {
            $table->dropColumn('passed_at');
        });
    }
}
