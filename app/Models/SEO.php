<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SEO extends Model
{
    protected $table = 'seo';
	protected $fillable = [
		'module_id', 'module_name', 'seo_title','seo_slug', 'seo_description','fb_image','fb_title','fb_description','tw_image',
'tw_title','tw_description'	];
}
