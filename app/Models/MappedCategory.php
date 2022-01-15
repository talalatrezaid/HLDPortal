<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappedCategory extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'store_id', 'store_front_category_id', 'product_category_id' ];
}
