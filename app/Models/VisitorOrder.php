<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id','voucher_id','user_id','invoice_number','total','grand_total',
        'payment_method','payment_status','payment_evidence','payment_reference_id',
        'payment_external_id','payment_id','payment_ownder_id',
        'notes','is_placed','has_withdrawn'
    ];

    public function details() {
        return $this->hasMany('App\Models\VisitorOrderDetail', 'order_id');
    }
    public function voucher() {
        return $this->belongsTo('App\Models\UserVoucher', 'voucher_id');
    }
    public function visitor() {
        return $this->belongsTo('App\Models\Visitor', 'visitor_id');
    }
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
