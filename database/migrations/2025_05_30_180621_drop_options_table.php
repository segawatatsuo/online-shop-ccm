<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('customers');//削除したいテーブル名
        Schema::drop('orders');//削除したいテーブル名
        Schema::drop('order_items');//削除したいテーブル名
        Schema::drop('deliveries');//削除したいテーブル名
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
