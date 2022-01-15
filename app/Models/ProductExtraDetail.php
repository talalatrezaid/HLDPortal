<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExtraDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'product_id',
        'upc',
        'bin_picking_number',
        'warranty',
        'search_keyword',
        'availability',
        'is_visible_on_site',
        'condition',
        'available_on',
        'availability_date',
        'featured',
        'show_condition_on_product',
        'sort_order',
        'order_minimum_quantity',
        'order_maximum_quantity',
    ];

    /**
     * Product_extra_detail record associated product.
     * Relation: each Product_extra_detail just has one product
     */
    public function extraDetailProduct(){

        return $this->belongsTo(Product::class);
    }
}
