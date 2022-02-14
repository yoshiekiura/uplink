<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id','user_id','invoice_number','total','grand_total',
        'payment_method','payment_status','payment_evidence',
        'is_placed'
    ];

    public function details() {
        return $this->hasMany('App\Models\VisitorOrderDetail', 'order_id');
    }
}
