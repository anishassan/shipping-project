<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'shipping_cost',
        'shipping_method',
        'shipping_carrier_id',
        'shipping_service_id',
        'shipping_tracking_number',
        'shipping_label_url',
        'shipping_address_id',
        'billing_address_id',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingCarrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class);
    }

    public function shippingService(): BelongsTo
    {
        return $this->belongsTo(ShippingService::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Methods
    public function calculateShippingCost()
    {
        if (!$this->shipping_carrier_id || !$this->shipping_service_id) {
            return 0;
        }

        $rate = ShippingRate::where('carrier_id', $this->shipping_carrier_id)
            ->where('service_id', $this->shipping_service_id)
            ->where('from_country', $this->shippingAddress->country)
            ->where('to_country', $this->billingAddress->country)
            ->first();

        if (!$rate) {
            return 0;
        }

        $totalWeight = $this->items->sum('weight');
        $shippingCost = $rate->base_rate;

        if ($totalWeight > $rate->min_weight) {
            $additionalWeight = $totalWeight - $rate->min_weight;
            $shippingCost += ($additionalWeight * $rate->additional_rate);
        }

        return $shippingCost;
    }

    public function generateShippingLabel()
    {
        if (!$this->shipping_carrier_id || !$this->shipping_service_id) {
            return false;
        }

        try {
            $carrier = $this->shippingCarrier;
            $service = $this->shippingService;

            // Create shipment record
            $shipment = Shipment::create([
                'order_id' => $this->id,
                'carrier_id' => $this->shipping_carrier_id,
                'service_id' => $this->shipping_service_id,
                'from_address_id' => $this->shippingAddress->id,
                'to_address_id' => $this->billingAddress->id,
                'weight' => $this->items->sum('weight'),
                'weight_unit' => 'kg',
                'status' => 'pending',
                'tracking_number' => $carrier->generateTrackingNumber(),
            ]);

            // Generate label through carrier API
            $label = $carrier->generateLabel($shipment);

            if ($label) {
                $shipment->update([
                    'label_url' => $label['url'],
                    'tracking_number' => $label['tracking_number'],
                ]);

                $this->update([
                    'shipping_tracking_number' => $label['tracking_number'],
                    'shipping_label_url' => $label['url'],
                    'status' => 'shipped',
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Error generating shipping label: ' . $e->getMessage());
            return false;
        }
    }
} 