<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unsignedBigInteger('variantId')->nullable();
            $table->string('title',400)->nullable();
            $table->string('price',200)->nullable();
            $table->string('sku',200)->nullable();
            $table->string('quantity',200)->nullable();
            $table->string('weight',200)->nullable();
            $table->string('weight_unit',200)->nullable();
            $table->string('shipping',200)->nullable();
            $table->string('taxable',200)->nullable();
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
        Schema::dropIfExists('product_variants');
    }
}
