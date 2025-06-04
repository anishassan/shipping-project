<?php

namespace App\Services;

use App\Models\ShippingAddress;
use App\Models\ShippingRate;
use App\Models\Shipment;
use App\Models\ShippingLabel;
use App\Models\ShippingTracker;
use EasyPost\EasyPost;
use EasyPost\Address;
use EasyPost\Shipment as EasyPostShipment;
use EasyPost\Rate;
use EasyPost\Tracker;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EasyPostService
{
    private $apiKey;
    private $testMode;

    public function __construct()
    {
        $this->apiKey = config('shipping.easypost.api_key');
        $this->testMode = config('shipping.easypost.test_mode', true);
        EasyPost::setApiKey($this->apiKey);
    }

    /**
     * Adres doğrulama ve kaydetme
     */
    public function verifyAddress(array $addressData): ShippingAddress
    {
        try {
            // EasyPost API ile adres doğrulama
            $address = Address::create($addressData);
            $verifiedAddress = $address->verify();

            // Doğrulanmış adresi veritabanına kaydet
            return ShippingAddress::create([
                'name' => $verifiedAddress->name,
                'company' => $verifiedAddress->company,
                'street1' => $verifiedAddress->street1,
                'street2' => $verifiedAddress->street2,
                'city' => $verifiedAddress->city,
                'state' => $verifiedAddress->state,
                'zip' => $verifiedAddress->zip,
                'country' => $verifiedAddress->country,
                'phone' => $verifiedAddress->phone,
                'email' => $verifiedAddress->email,
                'is_verified' => true,
                'verification_details' => $verifiedAddress->toArray()
            ]);
        } catch (Exception $e) {
            Log::error('Adres doğrulama hatası: ' . $e->getMessage());
            throw new Exception('Adres doğrulanamadı: ' . $e->getMessage());
        }
    }

    /**
     * Kargo fiyatlarını hesaplama
     */
    public function calculateRates(Shipment $shipment): array
    {
        try {
            // EasyPost shipment oluştur
            $easyPostShipment = EasyPostShipment::create([
                'from_address' => $shipment->fromAddress->toArray(),
                'to_address' => $shipment->toAddress->toArray(),
                'parcel' => [
                    'weight' => $shipment->weight,
                    'height' => $shipment->dimensions['height'] ?? null,
                    'width' => $shipment->dimensions['width'] ?? null,
                    'length' => $shipment->dimensions['length'] ?? null
                ]
            ]);

            // Fiyatları al ve veritabanına kaydet
            $rates = [];
            foreach ($easyPostShipment->rates as $rate) {
                $shippingRate = ShippingRate::create([
                    'carrier' => $rate->carrier,
                    'service' => $rate->service,
                    'rate' => $rate->rate,
                    'currency' => $rate->currency,
                    'delivery_days' => $rate->delivery_days,
                    'delivery_guarantee' => $rate->delivery_guarantee,
                    'carrier_details' => $rate->carrier_details,
                    'is_active' => true
                ]);
                $rates[] = $shippingRate;
            }

            return $rates;
        } catch (Exception $e) {
            Log::error('Fiyat hesaplama hatası: ' . $e->getMessage());
            throw new Exception('Fiyatlar hesaplanamadı: ' . $e->getMessage());
        }
    }

    /**
     * Kargo etiketi oluşturma
     */
    public function createLabel(Shipment $shipment, ShippingRate $rate): ShippingLabel
    {
        try {
            // EasyPost shipment oluştur ve etiket al
            $easyPostShipment = EasyPostShipment::create([
                'from_address' => $shipment->fromAddress->toArray(),
                'to_address' => $shipment->toAddress->toArray(),
                'parcel' => [
                    'weight' => $shipment->weight,
                    'height' => $shipment->dimensions['height'] ?? null,
                    'width' => $shipment->dimensions['width'] ?? null,
                    'length' => $shipment->dimensions['length'] ?? null
                ]
            ]);

            // Etiketi satın al
            $easyPostShipment->buy(['rate' => ['id' => $rate->id]]);

            // Etiketi indir ve kaydet
            $labelContent = file_get_contents($easyPostShipment->postage_label->label_url);
            $labelPath = 'shipping-labels/' . $shipment->tracking_number . '.pdf';
            Storage::put($labelPath, $labelContent);

            // Etiket bilgilerini veritabanına kaydet
            return ShippingLabel::create([
                'shipment_id' => $shipment->id,
                'label_url' => $easyPostShipment->postage_label->label_url,
                'label_file_path' => $labelPath,
                'label_format' => $easyPostShipment->postage_label->label_format,
                'label_size' => $easyPostShipment->postage_label->label_size,
                'label_details' => $easyPostShipment->postage_label->toArray(),
                'expires_at' => now()->addDays(30)
            ]);
        } catch (Exception $e) {
            Log::error('Etiket oluşturma hatası: ' . $e->getMessage());
            throw new Exception('Etiket oluşturulamadı: ' . $e->getMessage());
        }
    }

    /**
     * Kargo takibi
     */
    public function trackShipment(Shipment $shipment): ShippingTracker
    {
        try {
            // EasyPost tracker oluştur
            $tracker = Tracker::create([
                'tracking_code' => $shipment->tracking_number,
                'carrier' => $shipment->rate->carrier
            ]);

            // Takip bilgilerini veritabanına kaydet
            return ShippingTracker::create([
                'shipment_id' => $shipment->id,
                'tracking_code' => $tracker->tracking_code,
                'status' => $tracker->status,
                'tracking_details' => $tracker->tracking_details,
                'estimated_delivery_date' => $tracker->estimated_delivery_date,
                'delivered_at' => $tracker->delivered_at,
                'carrier_details' => $tracker->carrier_details
            ]);
        } catch (Exception $e) {
            Log::error('Kargo takip hatası: ' . $e->getMessage());
            throw new Exception('Kargo takip edilemedi: ' . $e->getMessage());
        }
    }

    /**
     * Kargo durumunu güncelleme
     */
    public function updateShipmentStatus(Shipment $shipment): void
    {
        try {
            $tracker = Tracker::retrieve($shipment->tracker->tracking_code);
            
            $shipment->tracker->update([
                'status' => $tracker->status,
                'tracking_details' => $tracker->tracking_details,
                'estimated_delivery_date' => $tracker->estimated_delivery_date,
                'delivered_at' => $tracker->delivered_at,
                'carrier_details' => $tracker->carrier_details
            ]);

            $shipment->update(['status' => $tracker->status]);
        } catch (Exception $e) {
            Log::error('Kargo durumu güncelleme hatası: ' . $e->getMessage());
            throw new Exception('Kargo durumu güncellenemedi: ' . $e->getMessage());
        }
    }

    /**
     * Kargo iptali
     */
    public function cancelShipment(Shipment $shipment): bool
    {
        try {
            $easyPostShipment = EasyPostShipment::retrieve($shipment->tracking_number);
            $refund = $easyPostShipment->refund();

            if ($refund->status === 'submitted') {
                $shipment->update(['status' => 'cancelled']);
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::error('Kargo iptal hatası: ' . $e->getMessage());
            throw new Exception('Kargo iptal edilemedi: ' . $e->getMessage());
        }
    }
} 