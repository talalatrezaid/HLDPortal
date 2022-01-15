<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellerFieldsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rezaid_users', function (Blueprint $table) {

            $table->string('seller_plan',200)->after('password')->nullable();
            $table->string('seller_phone',50)->after('seller_plan')->nullable();
            $table->string('seller_address')->after('seller_phone')->nullable();
            $table->string('seller_address2')->after('seller_address')->nullable();
            $table->string('seller_city',20)->after('seller_address2')->nullable();
            $table->string('seller_state',100)->after('seller_city')->nullable();
            $table->string('seller_country',20)->after('seller_state')->nullable();
            $table->string('seller_zipcode',20)->after('seller_country')->nullable();
            $table->string('seller_vatnumber',100)->after('seller_zipcode')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rezaid_users', function (Blueprint $table) {
            $table->dropColumn(['seller_plan']);
            $table->dropColumn(['seller_phone']);
            $table->dropColumn(['seller_address']);
            $table->dropColumn(['seller_address2']);
            $table->dropColumn(['seller_city']);
            $table->dropColumn(['seller_state']);
            $table->dropColumn(['seller_country']);
            $table->dropColumn(['seller_zipcode']);
            $table->dropColumn(['seller_vatnumber']);
        });
    }
}
