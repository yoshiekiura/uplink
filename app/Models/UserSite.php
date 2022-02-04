<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','seo_title','seo_description','pixel_tracking_id','analytics_tracking_id'
    ];
}
