<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // カラムの追加
            $table->string('sei')->nullable()->after('id');
            $table->string('mei')->nullable()->after('sei');
            $table->string('input_add01')->nullable()->after('phone');
            $table->string('input_add02')->nullable()->after('input_add01');
            $table->string('input_add03')->nullable()->after('input_add02');

            // カラムの削除
            $table->dropColumn('name');
            $table->dropColumn('address');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // 追加したカラムの削除
            $table->dropColumn('sei');
            $table->dropColumn('mei');
            $table->dropColumn('input_add01');
            $table->dropColumn('input_add02');
            $table->dropColumn('input_add03');

            // 削除したカラムを復元
            $table->string('name')->nullable(); // 元の型に合わせて
            $table->string('address')->nullable();
        });
    }
}
