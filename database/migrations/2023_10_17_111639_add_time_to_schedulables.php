<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeToSchedulables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedulables', function (Blueprint $table) {
            $table->time('drip_time')->after('drip_days')->format('H:i')->nullable();
            // ->default('08:00')
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedulables', function (Blueprint $table) {
            $table->dropColumn('drip_time');
        });
    }
}
