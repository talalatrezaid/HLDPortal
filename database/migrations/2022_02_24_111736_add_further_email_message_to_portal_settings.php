<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFurtherEmailMessageToPortalSettings extends Migration
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
            $table->string("welcome_email_message_user")->nullable();
            $table->string("welcome_email_message_charity")->nullable();
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
