<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPremium extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','payment_method','payment_status','payment_amount',
        'active_until','month_quantity','external_id','payment_link'
    ];
}
