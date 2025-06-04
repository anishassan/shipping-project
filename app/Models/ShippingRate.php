<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'carrier',
        'service',
        'rate',
        'currency',
        'delivery_days',
        'delivery_guarantee',
        'carrier_details',
        'is_active'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'delivery_days' => 'integer',
        'delivery_guarantee' => 'array',
        'carrier_details' => 'array',
        'is_active' => 'boolean'
    ];

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
