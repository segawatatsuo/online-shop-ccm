<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductImageJasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_image_jas', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('product_ja_id')->constrained('product_jas')->onDelete('cascade');
            $table->string('filename'); 
            $table->boolean('is_main')->default(false); // メイン画像かどうか
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
        Schema::dropIfExists('product_image_jas');
    }
}
