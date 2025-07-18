<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable()->comment('注文ID');
            $table->string('mail_type')->nullable()->comment('例: shipping_notification');
            $table->string('recipient_email')->nullable()->comment('送信先メールアドレス');
            $table->string('subject')->nullable()->comment('送信した件名');
            $table->text('body')->nullable()->comment('送信した本文');
            $table->string('status')->nullable()->comment('成功/失敗');
            $table->string('error_message')->nullable()->comment('エラーメッセージ');
            $table->string('sent_at')->nullable()->comment('送信日時');
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
        Schema::dropIfExists('mail_logs');
    }
}
