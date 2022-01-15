<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'api_key', 'api_password', 'api_domain', 'is_active'
    ];

    /**
     * User record associated with the stores.
     * Relation: each store just has one user
     */
    public function storeUser(){

        return $this->belongsTo(User::class);
    }

    /**
     * Store record associated with product.
     * Relation: each store may have more than one product
     */
    public function storeProducts()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Store record associated with product categories.
     * Relation: each store may have more than one product categories
     */
    public function storeProductCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }
}
