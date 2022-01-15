<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColNameToCategoryParentIdInStoreFrontCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_front_categories', function (Blueprint $table) {
            $table->renameColumn('type', 'categoryParentId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_front_categories', function (Blueprint $table) {
            $table->renameColumn('categoryParentId', 'type');
        });
    }
}
