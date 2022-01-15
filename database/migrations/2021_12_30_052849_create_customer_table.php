<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->increments("id");
            $table->string("email")->nullable();
            $table->string("accepts_marketing")->nullable();
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            $table->string("orders_count")->nullable();
            $table->string("state")->nullable();
            $table->string("total_spent")->nullable();
            $table->string("last_order_id")->nullable();
            $table->string("note")->nullable();
            $table->string("verified_email")->nullable();
            $table->string("multipass_identifier")->nullable();
            $table->string("tax_exempt")->nullable();
            $table->string("phone")->nullable();
            $table->string("tags")->nullable();
            $table->string("last_order_name")->nullable();
            $table->string("currency")->nullable();
            $table->string("accepts_marketing_updated_at")->nullable();
            $table->string("marketing_opt_in_level")->nullable();
            $table->string("sms_marketing_consent")->nullable();
            $table->string("admin_graphql_api_id")->nullable();
            $table->string("shopify_customer_id")->nullable();
            $table->string("default_address_address1")->nullable();
            $table->string("default_address_address2")->nullable();
            $table->string("default_address_city")->nullable();
            $table->string("default_address_province")->nullable();
            $table->string("default_address_country")->nullable();
            $table->string("default_address_zip")->nullable();
            $table->string("default_address_phone")->nullable();
            $table->string("default_address_province_code")->nullable();
            $table->string("default_address_country_code")->nullable();
            $table->string("default_address_country_name")->nullable();
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
        Schema::dropIfExists('customer');
    }
}
