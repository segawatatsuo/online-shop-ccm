<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdersTable3columns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_date')->nullable()->comment('配送希望日')->after('total_price');
            $table->string('delivery_time')->nullable()->comment('配送希望時間')->after('delivery_date');
            $table->text('your_request')->nullable()->comment('お客様のご要望')->after('delivery_time');
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
            $table->dropColumn('delivery_date');
            $table->dropColumn('delivery_time');
            $table->dropColumn('request');
        });
    }
}
