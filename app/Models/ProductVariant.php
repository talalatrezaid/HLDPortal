<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'product_id',
        'variantId',
        'title',
        'price',
        'weight',
        'weight_unit',
        'sku',
        'quantity',
        'shipping',
        'taxable',
        'inventory_item_id'
    ];

    /**
     * Product_variant record associated product.
     * Relation: each Product_variant just has one product
     */
    public function variantProduct()
    {

        return $this->belongsTo(Product::class);
    }

    /**
     * Product_variant record associated product_images.
     * Relation: each product_variant may have more than one product_image
     */
    public function variantImages()
    {

        return $this->hasMany(ProductImage::class);
    }

    /**
     * Product_variant record associated product_options.
     * Relation: each product_variant may have more than one product_option
     */
    public function variantOptions()
    {

        return $this->hasMany(ProductOption::class);
    }
}
