<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductJasTableWholesaleColum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_jas', function (Blueprint $table) {
            $table->boolean('wholesale')->nullable()->after('price')->comment('法人価格かどうか');
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
            $table->dropColumn('wholesale');
        });
    }
}
