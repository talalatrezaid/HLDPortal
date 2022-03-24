<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHermesColumnsToPortalSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_settings', function (Blueprint $table) {
            //
            $table->string("hermes_access_token_sandbox")->nullable();
            $table->string("hermes_api_url_sandbox")->nullable();
            $table->string("hermes_access_token_live")->nullable();
            $table->string("hermes_api_url_live")->nullable();
            $table->integer("is_hermes_live")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal_settings', function (Blueprint $table) {
            //
        });
    }
}
