<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInfoHexCode extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'product_custom_information_id', 'destination_country', 'hs_codes' ];

    /**
     * Product Info HexCode record associated with Product Custom Information.
     * Relation: each Product Info HexCode just has one Product Custom Information.
     */
    public function HexCodeProductCustomInformation(){

        return $this->belongsTo(ProductCustomInformation::class);
    }

}
