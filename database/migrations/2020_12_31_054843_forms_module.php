<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FormsModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('short_code',200)->unique();
            $table->text('form_data');
            $table->string('form_status',255);
            $table->text('form_title')->nullable();
            $table->text('form_description')->nullable();
            $table->text('mail_to')->nullable();
            $table->text('mail_from')->nullable();
            $table->text('mail_subject')->nullable();
            $table->text('email_attrs')->nullable();
            $table->text('message_body')->nullable();
            $table->text('file_attachments')->nullable();
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
        Schema::dropIfExists('forms');
    }
}
