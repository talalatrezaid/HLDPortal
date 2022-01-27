<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColorSettingsCharitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('color_settings_charities', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("charity_id");
            $table->string("primary_background_color")->default("#f5b012");
            $table->string("header_primary_color")->default("#ffffff");
            $table->string("footer_bg_secondary")->default("#13245d");
            $table->string("primary_buttons_color")->default("#007dc6");
            $table->string("secondary_brand_color")->default("#F7453A");
            $table->string("white_color")->default("#ffffff");
            $table->string("text-secondary")->default("#000");
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
        Schema::dropIfExists('color_settings_charities');
    }
}
