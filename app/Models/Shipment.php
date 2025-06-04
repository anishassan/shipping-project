<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_address_id',
        'to_address_id',
        'rate_id',
        'tracking_number',
        'status',
        'weight',
        'weight_unit',
        'dimensions',
        'customs_info',
        'insurance',
        'total_price',
        'currency',
        'metadata'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'customs_info' => 'array',
        'insurance' => 'array',
        'total_price' => 'decimal:2',
        'metadata' => 'array'
    ];

    public function fromAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'from_address_id');
    }

    public function toAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'to_address_id');
    }

    public function rate()
    {
        return $this->belongsTo(ShippingRate::class);
    }

    public function label()
    {
        return $this->hasOne(ShippingLabel::class);
    }

    public function tracker()
    {
        return $this->hasOne(ShippingTracker::class);
    }
}
