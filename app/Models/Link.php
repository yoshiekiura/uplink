<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','category_id','title','url','image','priority','description','clicked'
    ];

    public function category() {
        return $this->belongsTo('App\Models\UserCategory', 'category_id');
    }
}
