<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAFlagForCrmIrCsv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_settings', function (Blueprint $table) {
            // adding this flag for superadmin
            // a flag that will include amount of additional products in csv 
            // (only for islamic relief csv)
            $table->integer("is_include_addional_products")->default(0);
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
