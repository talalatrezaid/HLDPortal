<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CharityDonation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charity_donation', function (Blueprint $table) {
            //
            $table->increments("id");
            $table->unsignedInteger("order_id"); // local db id
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedInteger("project_id"); // local db id
            $table->string("project_name"); // local db id
            $table->double("amount")->default(0); // local db id
            $table->integer("charity_id"); // local db id
            $table->integer("refund")->default(0); // local db id
            $table->double("refund_amount")->default(0); // local db id
            $table->double("refund_reason")->nullable(); // local db id
            $table->integer("status"); // local db id


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
