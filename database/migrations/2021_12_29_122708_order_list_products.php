<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderListProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_list_of_products', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("order_id"); // local db id
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer("shopify_id"); // id  
            $table->string("fulfillable_quantity")->nullable();
            $table->string("fulfillment_service")->nullable();
            $table->string("fulfillment_status")->nullable();
            $table->string("gift_card")->nullable();
            $table->string("grams")->nullable();
            $table->string("name")->nullable();
            $table->string("price")->nullable();
            $table->string("product_exists")->nullable();
            $table->string("product_id")->nullable();
            $table->string("quantity")->nullable();
            $table->string("requires_shipping")->nullable();
            $table->string("sku")->nullable();
            $table->string("taxable")->nullable();
            $table->string("title")->nullable();
            $table->string("total_discount")->nullable();
            $table->string("variant_id")->nullable();
            $table->string("variant_inventory_management")->nullable();
            $table->string("variant_title")->nullable();
            $table->string("vendor")->nullable();
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
        //
    }
}
