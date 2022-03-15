<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','url','type','play_count','title','priority'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
