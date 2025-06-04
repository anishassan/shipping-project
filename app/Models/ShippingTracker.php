<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingTracker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_id',
        'tracking_code',
        'status',
        'tracking_details',
        'estimated_delivery_date',
        'delivered_at',
        'carrier_details'
    ];

    protected $casts = [
        'tracking_details' => 'array',
        'estimated_delivery_date' => 'datetime',
        'delivered_at' => 'datetime',
        'carrier_details' => 'array'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
