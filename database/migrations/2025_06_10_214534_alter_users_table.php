<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin'); // is_admin カラムを削除
            $table->dropColumn('postal_code'); // postal_code カラムを削除
            $table->dropColumn('address'); // address カラムを削除
            $table->dropColumn('phone'); // phone カラムを削除

            $table->string('name')->nullable()->change(); // name カラムを null 許容に変更
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('is_admin')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
        });
    }
}
