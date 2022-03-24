<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingWeightSlots extends Model
{
    protected $table = 'shipping_weight_slots';
    use HasFactory;
    protected $fillable = ["min_weight", "max_weight", "charges"];
}
