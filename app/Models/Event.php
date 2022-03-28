<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','category_id','title','description','cover','platform','platform_url',
        'date','duration','price','price_sale','quantity','custom_message',
        'action_button_text'
    ];

    public function category() {
        return $this->belongsTo('App\Models\UserCategory', 'category_id');
    }
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
