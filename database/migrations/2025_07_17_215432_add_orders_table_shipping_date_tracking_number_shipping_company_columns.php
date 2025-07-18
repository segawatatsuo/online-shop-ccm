<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdersTableShippingDateTrackingNumberShippingCompanyColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_date')->nullable()->comment('発送日')->after('total_price');
            $table->string('tracking_number')->nullable()->comment('配送伝票番号')->after('shipping_date');
            $table->string('shipping_company')->nullable()->comment('運送会社名')->after('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_date');
            $table->dropColumn('tracking_number');
            $table->dropColumn('shipping_company');
        });
    }
}
