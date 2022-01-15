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
        'name','email','password','username','phone','categories','icon','bio',
        'background_image','token'
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
    public function links() {
        return $this->hasMany('App\Models\Link', 'user_id');
    }
    public function data_categories() {
        return $this->hasMany('App\Models\UserCategory', 'user_id');
    }

}
