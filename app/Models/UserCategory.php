<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCategory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','image','has_used'];

    public function links() {
        return $this->hasMany('App\Models\Link', 'category_id');
    }
    public function videos() {
        return $this->hasMany('App\Models\Video', 'category_id');
    }
    public function digital_products() {
        return $this->hasMany('App\Models\DigitalProduct', 'category_id');
    }
    public function products() {
        return $this->hasMany('App\Models\Product', 'category_id');
    }
    public function events() {
        return $this->hasMany('App\Models\Event', 'category_id');
    }
}
