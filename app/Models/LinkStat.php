<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_id','count','date'
    ];

    public function link() {
        return $this->belongsTo('App\Models\Link', 'link_id');
    }
}
