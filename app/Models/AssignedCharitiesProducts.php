<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignedCharitiesProducts extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'charity_id', 'variantId', 'qty'];

    /**
     * Every assigned product have a product and product variant data.
     * Relation: with product.
     */
    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
