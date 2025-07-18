<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTopPagesTable2columns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('top_pages', function (Blueprint $table) {
            $table->string('hero_head_copy_color')->nullable()->after('hero_head_copy')->comment('文字色');
            $table->string('hero_lead_copy_color')->nullable()->after('hero_lead_copy')->comment('文字色');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('top_pages', function (Blueprint $table) {
            $table->dropColumn('hero_head_copy_color');
            $table->dropColumn('hero_lead_copy_color');
        });
    }
}
