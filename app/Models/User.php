<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'rezaid_users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'seller_plan',
        'seller_phone',
        'seller_address',
        'seller_address2',
        'seller_city',
        'seller_state',
        'seller_country',
        'seller_zipcode',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Users record associated with stores.
     * Relation: each user may have more than one store
     */
    public function userStores(){

        return $this->hasMany(Store::class);
    }

    /**
     * User record associated with products.
     * Relation: each user may have more than one product
     */
    public function userProducts(){

        return $this->hasMany(Product::class);
    }
}
