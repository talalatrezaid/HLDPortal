<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductExtraDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_extra_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            //$table->string('store_name',400)->nullable();
            $table->string('upc',200)->nullable();
            $table->string('bin_picking_number',200)->nullable();
            $table->string('warranty',200)->nullable();
            $table->string('search_keyword',1000)->nullable();
            $table->string('availability',2000)->nullable();
            $table->enum('is_visible_on_site', ['0', '1'])->default('1');
            $table->string('condition',30)->nullable();
            $table->enum('available_on', ['0', '1'])->default('1');
            $table->dateTime('availability_date')->nullable();
            $table->enum('featured', ['0', '1'])->default('0');
            $table->enum('show_condition_on_product', ['0', '1'])->default('0');
            $table->integer('sort_order')->nullable();
            $table->integer('order_minimum_quantity')->nullable();
            $table->integer('order_maximum_quantity')->nullable();
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
        Schema::dropIfExists('product_extra_details');
    }
}
