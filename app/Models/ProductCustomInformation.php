<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCustomInformation extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'product_id', 'country_of_origin', 'commodity_description' ];

    /**
     * Product_custom_information associated with product_info_hex_code.
     * Relation: each Product_custom_information may have more than one product_info_hex_code.
     */
    public function productCustomInformationHexCodes(){

        return $this->hasMany(ProductInfoHexCode::class);

    }

    /**
     * Product_custom_information record associated product.
     * Relation: each Product_custom_information just has one product
     */
    public function productCustomInformationProduct(){

        return $this->belongsTo(Product::class);
    }
}
