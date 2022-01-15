<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductInfoHexCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_info_hex_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_custom_information_id');
            $table->foreign('product_custom_information_id')->references('id')->on('product_custom_information');
            $table->string('destination_country',200)->nullable();
            $table->string('hs_codes',200)->nullable();
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
        Schema::dropIfExists('product_info_hex_codes');
    }
}
