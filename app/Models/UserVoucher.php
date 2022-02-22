<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','code','discount_type','amount','expiration','quantity'
    ];

    public function usages() {
        return $this->hasMany('App\Models\VisitorOrder', 'voucher_id');
    }
}
