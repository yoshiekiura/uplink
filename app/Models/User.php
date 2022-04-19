<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name','email','is_email_activated','password','username','phone','categories','icon','bio',
        'background_image','token','pro_expiration','province_id','city_id','subdistrict_id','address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function socials() {
        return $this->hasMany('App\Models\SocialLink', 'user_id');
    }
    public function events() {
        return $this->hasMany('App\Models\Event', 'user_id');
    }
    public function links() {
        return $this->hasMany('App\Models\Link', 'user_id');
    }
    public function supports() {
        return $this->hasMany('App\Models\Support', 'user_id');
    }
    public function user_categories() {
        return $this->hasMany('App\Models\UserCategory', 'user_id');
    }
    public function banks() {
        return $this->hasMany('App\Models\UserBank', 'user_id');
    }
    public function site() {
        return $this->hasOne('App\Models\UserSite', 'user_id');
    }
    public function premium() {
        return $this->hasOne('App\Models\UserPremium', 'user_id')->latest();
    }

}
