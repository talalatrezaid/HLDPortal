<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'product_id',
        'product_variant_id',
        'imageId',
        'source',
    ];

    /**
     * Product_image record associated product.
     * Relation: each Product_image just has one product
     */
    public function imageProduct()
    {

        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Product_image record associated product_variant.
     * Relation: each Product_image just has one product_variant
     */
    public function imageVariant()
    {

        return $this->belongsTo(ProductVariant::class);
    }
}
