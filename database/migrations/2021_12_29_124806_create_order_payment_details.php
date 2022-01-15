<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPaymentDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payment_details', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("order_id"); // local db id
            $table->foreign('order_id')->references('id')->on('orders');

            $table->string("credit_card_bin")->nullable();
            $table->string("avs_result_code")->nullable();
            $table->string("cvv_result_code")->nullable();
            $table->string("credit_card_number")->nullable();
            $table->string("credit_card_company")->nullable();
            $table->string("credit_card_name")->nullable();
            $table->string("credit_card_wallet")->nullable();
            $table->string("credit_card_expiration_month")->nullable();
            $table->string("credit_card_expiration_year")->nullable();
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
        Schema::dropIfExists('order_payment_details');
    }
}
