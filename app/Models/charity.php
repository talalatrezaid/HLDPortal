<?php

namespace App\Models;

use App\Models\Orders\Orders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class charity extends Model
{
    use HasFactory, SoftDeletes;


    // user of an admin called charity
    // there are 2 tables 
    // #1 rezaid_users which is using for superadmin linked with laravel
    // #2 table user in database means 1 charity user admin that can handle his/her website page 

    protected $table = 'user';
    protected $softDelete = true;

    protected $fillable = [
        'user_name', //mean slug
        'charity_name', //mean admin title
        'email',
        'password',
        'user_type_id',
        'is_active',
        'last_login',
        'total_login',
        'total_orders',
        'total_charity_recieved'

    ];

    public function charity()
    {
        return $this->hasMany(Orders::class, 'charity_id', 'id');
    }
}
