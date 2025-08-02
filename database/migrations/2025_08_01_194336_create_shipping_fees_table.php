<?php

// database/migrations/xxxx_xx_xx_create_shipping_fees_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingFeesTable extends Migration
{
    public function up()
    {
        Schema::create('shipping_fees', function (Blueprint $table) {
            $table->id();
            $table->string('prefecture'); // 例: 東京都
            $table->unsignedInteger('fee'); // 送料（円）
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_fees');
    }
}
