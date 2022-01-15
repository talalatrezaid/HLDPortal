<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'product_id',
        'product_variant_id',
        'optionId',
        'name',
        'value',
    ];

    /**
     * Product_option record associated product.
     * Relation: each Product_option just has one product
     */
    public function optionProduct(){

        return $this->belongsTo(Product::class);
    }


    /**
     * Product_option record associated product_variant.
     * Relation: each Product_option just has one product_variant
     */
    public function optionVariant(){

        return $this->belongsTo(ProductVariant::class);
    }


}
