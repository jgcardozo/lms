<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClassMarkerResultsCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('class_marker_results', function (Blueprint $table) {
            $table->longText('cert_url')->after('passed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_marker_results', function (Blueprint $table) {
            $table->dropColumn('cert_url');
        });
    }
}
