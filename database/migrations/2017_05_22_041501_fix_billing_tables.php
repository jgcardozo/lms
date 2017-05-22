<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixBillingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('is_course_products');

        Schema::table('courses', function (Blueprint $table) {
            $table->text('billing_is_products')->nullable()->after('user_lock_date');
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
			$table->dropColumn('billing_is_products');
		});
    }
}
