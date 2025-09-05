<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeStatusColumnInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // まず既存のNULL値を確認・更新（必要に応じて）
        // DB::table('orders')->whereNull('status')->update(['status' => 0]);
        
        DB::statement('ALTER TABLE orders MODIFY COLUMN status TINYINT NOT NULL COMMENT "数値"');
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        // NOT NULLを元に戻す（NULLを許可する）
        DB::statement('ALTER TABLE orders MODIFY COLUMN status TINYINT NULL COMMENT "数値"');
        
        // または元の状態が異なるデータ型だった場合は適切に変更
        // DB::statement('ALTER TABLE orders MODIFY COLUMN status VARCHAR(255) NULL');
    });
}
}
