<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductJasTable3columns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_jas', function (Blueprint $table) {
            $table->string('description_1_heading')->nullable()->after('name');
            $table->string('description_2_heading')->nullable()->after('description_1');
            $table->text('description_2')->nullable()->after('description_2_heading');
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
            //
        });
    }
}
