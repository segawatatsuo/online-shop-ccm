<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_pages', function (Blueprint $table) {
            $table->id();
            $table->string('category')->comment('カテゴリは必須項目です');
            $table->string('hero_img')->comment('topページのヒーローイメージ');
            $table->string('head_copy')->comment('topページのヘッドコピー');

            $table->integer('section1_display_hide')->nullable()->comment('表示・非表示 1か0かnull');
            $table->string('section1_img')->nullable()->comment('セクション1の画像');
            
            $table->string('section1_head_copy')->nullable()->comment('セクション1のヘッドコピー');
            $table->text('section1_copy')->nullable()->comment('セクション1のコピー');
            $table->string('section1_background_color')->nullable()->comment('セクション1の背景色');

            $table->integer('section2_display_hide')->nullable()->comment('表示・非表示 1か0かnull');
            $table->string('section2_img')->nullable()->comment('セクション2の画像');
            $table->string('section2_head_copy')->nullable()->comment('セクション2のヘッドコピー');
            $table->text('section2_copy')->nullable()->comment('セクション2のcopy');
            $table->string('section2_background_color')->nullable()->comment('セクション2の背景色');

            $table->integer('section3_display_hide')->nullable()->comment('表示・非表示 1か0かnull');
            $table->string('section3_img')->nullable()->comment('セクション3の画像');
            $table->string('section3_head_copy')->nullable()->comment('セクション3のヘッドコピー');
            $table->text('section3_copy')->nullable()->comment('セクション3のcopy');
            $table->string('section3_background_color')->nullable()->comment('セクション3の背景色');

            $table->integer('section4_display_hide')->nullable()->comment('表示・非表示 1か0かnull');
            $table->string('section4_img')->nullable()->comment('セクション4の画像');
            $table->string('section4_head_copy')->nullable()->comment('セクション4のヘッドコピー');
            $table->text('section4_copy')->nullable()->comment('セクション4のcopy');
            $table->string('section4_background_color')->nullable()->comment('セクション4の背景色');

            $table->integer('movie_section_display_hide')->nullable()->comment('表示・非表示 1か0かnull');
            $table->string('movie_section')->nullable()->comment('動画URL');

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
        Schema::dropIfExists('top_pages');
    }
}
