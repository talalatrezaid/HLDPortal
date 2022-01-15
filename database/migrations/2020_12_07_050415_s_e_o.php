<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SEO extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SEO', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id')->nullable();
            $table->text('module_name')->nullable();
            $table->mediumText('seo_title')->nullable();
            $table->mediumText('seo_slug')->nullable();
            $table->longText('seo_description')->nullable();
            $table->string('fb_image')->nullable();
            $table->mediumText('fb_title')->nullable();
            $table->longText('fb_description')->nullable();
            $table->text('tw_image')->nullable();
            $table->mediumText('tw_title')->nullable();
            $table->longText('tw_description')->nullable();
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
        Schema::dropIfExists('SEO');
    }
}
