<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id','user_id',
        'name','description','platform','url','price','quantity','custom_message'
    ];

    public function images() {
        return $this->hasMany('App\Models\DigitalProductImage', 'product_id');
    }
}
