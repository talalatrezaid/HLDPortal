<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersInsert extends Model
{
	protected $table = 'rezaid_users';
	protected $fillable = [
		'name', 'user_name', 'email','password','role', 
	];
}