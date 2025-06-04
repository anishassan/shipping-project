<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company',
        'street1',
        'street2',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'email',
        'is_verified',
        'verification_details'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verification_details' => 'array'
    ];

    public function fromShipments()
    {
        return $this->hasMany(Shipment::class, 'from_address_id');
    }

    public function toShipments()
    {
        return $this->hasMany(Shipment::class, 'to_address_id');
    }
}
