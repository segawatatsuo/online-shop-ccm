<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('sei')->comment('姓');
            $table->string('mei')->comment('名');
            $table->string('email')->comment('メールアドレス');
            $table->string('phone')->comment('電話番号');
            $table->string('zip')->comment('郵便番号');
            $table->string('input_add01')->comment('都道府県');
            $table->string('input_add02')->comment('市区町村')->nullable();
            $table->string('input_add03')->comment('市区町村以降の住所')->nullable();
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
        Schema::dropIfExists('deliveries');
    }
}
