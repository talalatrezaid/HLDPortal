<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TeamsModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->string('slug',200)->unique();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->longText('makes_us_team')->nullable();
            $table->longText('makes_us_team_custom')->nullable();
            $table->longText('teams_content')->nullable();
            $table->longText('senior_management')->nullable();
            $table->longText('development_team')->nullable();
            $table->longText('design_team')->nullable();
            $table->longText('join_our_team_content')->nullable();
            $table->string('join_our_team_image')->nullable();
            $table->longText('join_team_button_text')->nullable();
            $table->longText('join_team_button_link')->nullable();
            $table->longText('office_photos')->nullable();
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
        Schema::dropIfExists('teams');
    }
}
