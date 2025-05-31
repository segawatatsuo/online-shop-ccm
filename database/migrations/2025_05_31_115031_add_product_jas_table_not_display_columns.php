<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductJasTableNotDisplayColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_jas', function (Blueprint $table) {
            $table->boolean('not_display')->default(false)->after('category_id')->comment('非表示フラグ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_jas', function (Blueprint $table) {
            $table->dropColumn('not_display');
        });
    }
}
