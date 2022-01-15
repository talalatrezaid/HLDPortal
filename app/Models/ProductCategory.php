<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'store_id', 'categoryId', 'name' ];

    /**
     * Product category record associated with store.
     * Relation: each product category just has one store
     */
    public function productCategoryStore(){

        return $this->belongsTo(Store::class);
    }

    /**
     * Product categories record associated with products.
     * Relation: each product category may have more than one products
     */
    public function productCategoryProducts(){

        return $this->belongsToMany(Product::class);

    }

    /**
     * Product categories associated with mapped_categories.
     * Relation: each Product category may have more than one mapped_category
     */
    public function productCategoryMappedCategories(){

        return $this->hasMany(MappedCategory::class);

    }
}
