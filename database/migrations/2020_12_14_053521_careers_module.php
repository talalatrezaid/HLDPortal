<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CareersModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('subtitle')->nullable();
            $table->string('slug',200)->unique();
            $table->string('job_status')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->text('location')->nullable();
            $table->string('department')->nullable();
            $table->string('job_type')->nullable();
            $table->longText('requirements_and_duties')->nullable();
            $table->longText('skills_and_abilities')->nullable();
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
        Schema::dropIfExists('careers');
    }
}
