<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBillingAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_billing_address', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("order_id"); // local db id

            $table->string("first_name")->nullable();
            $table->string("address1")->nullable();
            $table->string("phone")->nullable();
            $table->string("city")->nullable();
            $table->string("zip")->nullable();
            $table->string("province")->nullable();
            $table->string("country")->nullable();
            $table->string("last_name")->nullable();
            $table->string("address2")->nullable();
            $table->string("company")->nullable();
            $table->string("latitude")->nullable();
            $table->string("longitude")->nullable();
            $table->string("name")->nullable();
            $table->string("country_code")->nullable();
            $table->string("province_code")->nullable();
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
        Schema::dropIfExists('order_billing_address');
    }
}
