<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','product_type',
        'event','digital_product','physical_product','support','chat_subscription',
        'quantity','total_price',
        'shipping_origin','shipping_address','shipping_waybill','shipping_courier','shipping_cost'
    ];

    public function event_item() {
        return $this->belongsTo('App\Models\Event', 'event');
    }
    public function digital_product_item() {
        return $this->belongsTo('App\Models\DigitalProduct', 'digital_product');
    }
    public function physical_product_item() {
        return $this->belongsTo('App\Models\PhysicalProduct', 'physical_product');
    }
    public function support_item() {
        return $this->belongsTo('App\Models\Support', 'support');
    }
    public function order() {
        return $this->belongsTo('App\Models\VisitorOrder', 'order_id');
    }
}
