<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portal_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('testing_worldpay_client_id');
            $table->string('testing_worldpay_secret_key');
            $table->string('live_worldpay_client_id');
            $table->string('live_worldpay_secret_key');
            $table->integer('is_live_worldpay');
            $table->text('welcome_charity_email_messsage');
            $table->text('assigning_product_email_message');
            $table->text('customer_order_email_message');
            $table->text('charity_order_email_message');
            $table->text('superadmin_email_message');
            $table->text('website_notify_email');
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
        Schema::dropIfExists('portal_settings');
    }
}
