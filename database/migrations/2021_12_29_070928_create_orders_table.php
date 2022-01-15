<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("order_id");
            $table->integer("charity_id"); //local db
            $table->foreign('charity_id')->references('id')->on('user'); // becasue user table is being used as charities

            $table->unsignedBigInteger("store_id"); // local db 
            $table->foreign('store_id')->references('id')->on('stores');

            $table->integer("is_shopify_order"); // local db  1 mean order took from shopify 0 mean order from our portal
            $table->text("cancel_reason")->nullable(); // shopify
            $table->string("cancelled_at")->nullable(); // shopify
            $table->string("cart_token")->nullable();  // shopify
            $table->string("checkout_id")->nullable();  // shopify
            $table->string("checkout_token")->nullable();  // shopify
            $table->string("client_details")->nullable();  // shopify
            $table->string("closed_at")->nullable();  // shopify
            $table->boolean("confirmed")->default(FALSE);  // shopify
            $table->string("contact_email")->nullable();  // shopify

            $table->string("currency")->nullable();  // shopify
            $table->string("current_subtotal_price")->nullable();  // shopify
            $table->string("current_total_discounts")->nullable();  // shopify
            $table->string("current_total_price")->nullable();  // shopify
            $table->string("current_total_tax")->nullable();  // shopify

            // i have listed all attributes which i got in this date 
            // if in future there will be more attributes please add by yourself
            // in future you can make this field i am commenting it because i don't need it in my database 
            // because in current situation we are handlling only UK orders for only holylanddates store 
            //$table->string("current_total_duties_set")->nullable();  // shopify
            //$table->string("total_discounts_set")->nullable();  // shopify
            //$table->string("current_total_price_set")->nullable();  // shopify
            //$table->string("current_subtotal_price_set")->nullable();  // shopify
            //$table->string("current_total_tax_set")->nullable();  // shopify
            //$table->string("discount_codes")->nullable();  // shopify
            //$table->string("note_attributes")->nullable();  // shopify
            //$table->string("original_total_duties_set")->nullable();  // shopify
            //$table->string("total_line_items_price_set")->nullable();  // shopify
            //$table->string("total_price_set")->nullable();  // shopify
            //$table->string("total_tax_set")->nullable();  // shopify
            //$table->string("total_shipping_price_set")->nullable();  // shopify

            $table->string("email")->nullable();  // shopify
            $table->string("estimated_taxes")->nullable();  // shopify
            $table->string("financial_status")->nullable();  // shopify
            $table->string("fulfillment_status")->nullable();  // shopify
            $table->string("gateway")->nullable();  // shopify
            $table->string("landing_site")->nullable();  // shopify
            $table->string("landing_site_ref")->nullable();  // shopify
            $table->string("location_id")->nullable();  // shopify
            $table->string("name")->nullable();  // shopify
            $table->string("note")->nullable();  // shopify
            $table->string("number")->nullable();  // shopify
            $table->string("order_number")->nullable();  // shopify
            $table->string("order_status_url")->nullable();  // shopify
            //it should be comma seperated
            $table->string("payment_gateway_names")->nullable();  // shopify
            $table->string("phone")->nullable();  // shopify
            $table->string("presentment_currency")->nullable();  // shopify
            $table->string("processed_at")->nullable();  // shopify
            $table->string("processing_method")->nullable();  // shopify
            $table->string("reference")->nullable();  // shopify
            $table->string("referring_site")->nullable();  // shopify
            $table->string("source_identifier")->nullable();  // shopify
            $table->string("source_name")->nullable();  // shopify
            $table->string("source_url")->nullable();  // shopify
            $table->string("subtotal_price")->nullable();  // shopify
            $table->string("tags")->nullable();  // shopify
            $table->string("taxes_included")->nullable();  // shopify
            $table->string("test")->nullable();  // shopify
            $table->string("token")->nullable();  // shopify
            $table->string("total_discounts")->nullable();  // shopify
            $table->string("total_line_items_price")->nullable();  // shopify
            $table->string("total_outstanding")->nullable();  // shopify
            $table->string("total_price")->nullable();  // shopify
            $table->string("total_price_usd")->nullable();  // shopify
            $table->string("total_tax")->nullable();  // shopify
            $table->string("total_tip_received")->nullable();  // shopify
            $table->string("total_weight")->nullable();  // shopify
            $table->string("user_id")->nullable();  // shopify
            $table->unsignedInteger("customer_id")->nullable();  // shopify
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
        Schema::dropIfExists('orders');
    }
}
