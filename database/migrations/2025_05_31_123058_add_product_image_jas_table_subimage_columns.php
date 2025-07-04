<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductImageJasTableSubimageColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_image_jas', function (Blueprint $table) {
            $table->string('is_sub')->nullable()->comment('サブイメージ')->after('is_main');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_image_jas', function (Blueprint $table) {
            $table->dropColumn('is_sub');
        });
    }
}
