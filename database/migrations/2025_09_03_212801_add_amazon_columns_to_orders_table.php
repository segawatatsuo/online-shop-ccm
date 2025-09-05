<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmazonColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('amazon_charge_permission_id')->nullable()->comment('Webhook で必ず来る ID。注文を特定するキーになる')->after('amazon_checkout_session_id');
            $table->string('amazon_charge_id')->nullable()->comment('個々のチャージ（売上確定/キャンセルなど）に対応する ID')->after('amazon_charge_permission_id');
            $table->string('payment_status')->nullable()->comment('Amazon 側の状態（例: Authorized, Captured, Canceled）')->after('status');
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
            $table->dropColumn('amazon_charge_permission_id');
            $table->dropColumn('amazon_charge_id');
            $table->dropColumn('payment_status');
        });
    }
}
