<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFullfilments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_fullfilments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("order_id"); // local db id
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string("shopify_id")->nullable();
            $table->string("admin_graphql_api_id")->nullable();
            $table->string("location_id")->nullable();
            $table->string("name")->nullable();
            $table->string("shopify_order_id")->nullable();
            $table->string("service")->nullable();
            $table->string("shipment_status")->nullable();
            $table->string("status")->nullable();
            $table->string("tracking_company")->nullable();
            $table->string("tracking_number")->nullable();
            $table->string("tracking_url")->nullable();
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
        Schema::dropIfExists('order_fullfilments');
    }
}
