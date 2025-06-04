<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingLabel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_id',
        'label_url',
        'label_file_path',
        'label_format',
        'label_size',
        'label_details',
        'expires_at'
    ];

    protected $casts = [
        'label_details' => 'array',
        'expires_at' => 'datetime'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
