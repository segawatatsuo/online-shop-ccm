<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_customers', function (Blueprint $table) {
            $table->id();
            $table->string('order_company_name')->nullable();
            $table->string('order_department')->nullable()->comment('部署名');
            $table->string('order_sei')->nullable();
            $table->string('order_mei')->nullable();
            $table->string('order_phone')->nullable();
            $table->string('homepage')->nullable();
            $table->string('email')->nullable();
            $table->string('order_zip')->nullable();
            $table->string('order_add01')->nullable();
            $table->string('order_add02')->nullable();
            $table->string('order_add03')->nullable();
            $table->string('same_as_orderer')->nullable();

            $table->string('delivery_company_name')->nullable();
            $table->string('delivery_department')->nullable()->comment('部署名');
            $table->string('delivery_sei')->nullable();
            $table->string('delivery_mei')->nullable();
            //$table->string('delivery_phone')->nullable();
            //$table->string('delivery_email')->nullable();
            $table->string('delivery_zip')->nullable();
            $table->string('delivery_add01')->nullable();
            $table->string('delivery_add02')->nullable();
            $table->string('delivery_add03')->nullable();
            $table->string('corporate_number')->nullable()->comment('法人番号'); // 法人番号
            $table->decimal('discount_rate')->nullable()->comment('割引率');
            $table->boolean('is_approved')->default(true)->comment('承認状態');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_customers');
    }
}
