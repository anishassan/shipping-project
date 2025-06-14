<?php

namespace App\Services;

use App\Models\ShippingAddress;
use App\Models\ShippingRate;
use App\Models\Shipment;
use App\Models\ShippingLabel;
use App\Models\ShippingTracker;
use EasyPost\EasyPostClient;
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
    private $client;

    public function __construct()
    {
        $this->apiKey = env("EASYPOST_API_KEY");
        
        //config('shipping.easypost.api_key');
        $this->testMode = config('shipping.easypost.test_mode', true);
        
        // Create client with timeout settings to handle network issues
        try {
            $this->client = new EasyPostClient($this->apiKey);
        } catch (Exception $e) {
            Log::error('EasyPost client creation failed: ' . $e->getMessage());
            throw new Exception('EasyPost service initialization failed. Please check your internet connection and try again.');
        }
    }

    /**
     * Adres doğrulama ve kaydetme
     */
    public function verifyAddress(array $addressData): ShippingAddress
    {
        try {
            // Log the address data being sent
            Log::info('Address verification attempt', ['address_data' => $addressData]);
            
            // EasyPost API ile adres doğrulama
            $address = $this->client->address->create($addressData);
            
            // Log the response
            Log::info('EasyPost address response', ['address_id' => $address->id ?? 'unknown']);
            
            // Check if address was created successfully
            if (!$address) {
                throw new Exception('Address creation failed');
            }

            // Doğrulanmış adresi veritabanına kaydet
            return ShippingAddress::create([
                'name' => $address->name ?? '',
                'company' => $address->company ?? '',
                'street1' => $address->street1 ?? '',
                'street2' => $address->street2 ?? '',
                'city' => $address->city ?? '',
                'state' => $address->state ?? '',
                'zip' => $address->zip ?? '',
                'country' => $address->country ?? '',
                'phone' => $address->phone ?? '',
                'email' => $address->email ?? '',
                'is_verified' => true,
                'verification_details' => [
                    'id' => $address->id ?? '',
                    'name' => $address->name ?? '',
                    'company' => $address->company ?? '',
                    'street1' => $address->street1 ?? '',
                    'street2' => $address->street2 ?? '',
                    'city' => $address->city ?? '',
                    'state' => $address->state ?? '',
                    'zip' => $address->zip ?? '',
                    'country' => $address->country ?? '',
                    'phone' => $address->phone ?? '',
                    'email' => $address->email ?? '',
                    'verifications' => $address->verifications ?? null
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Adres doğrulama hatası: ' . $e->getMessage());
            Log::error('Address data: ' . json_encode($addressData));
            Log::error('API Key: ' . substr($this->apiKey, 0, 10) . '...');
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
            $easyPostShipment = $this->client->shipment->create([
                'from_address' => [
                    'name' => $shipment->fromAddress->name,
                    'company' => $shipment->fromAddress->company,
                    'street1' => $shipment->fromAddress->street1,
                    'street2' => $shipment->fromAddress->street2,
                    'city' => $shipment->fromAddress->city,
                    'state' => $shipment->fromAddress->state,
                    'zip' => $shipment->fromAddress->zip,
                    'country' => $shipment->fromAddress->country,
                    'phone' => $shipment->fromAddress->phone,
                    'email' => $shipment->fromAddress->email
                ],
                'to_address' => [
                    'name' => $shipment->toAddress->name,
                    'company' => $shipment->toAddress->company,
                    'street1' => $shipment->toAddress->street1,
                    'street2' => $shipment->toAddress->street2,
                    'city' => $shipment->toAddress->city,
                    'state' => $shipment->toAddress->state,
                    'zip' => $shipment->toAddress->zip,
                    'country' => $shipment->toAddress->country,
                    'phone' => $shipment->toAddress->phone,
                    'email' => $shipment->toAddress->email
                ],
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
            $easyPostShipment = $this->client->shipment->create([
                'from_address' => [
                    'name' => $shipment->fromAddress->name,
                    'company' => $shipment->fromAddress->company,
                    'street1' => $shipment->fromAddress->street1,
                    'street2' => $shipment->fromAddress->street2,
                    'city' => $shipment->fromAddress->city,
                    'state' => $shipment->fromAddress->state,
                    'zip' => $shipment->fromAddress->zip,
                    'country' => $shipment->fromAddress->country,
                    'phone' => $shipment->fromAddress->phone,
                    'email' => $shipment->fromAddress->email
                ],
                'to_address' => [
                    'name' => $shipment->toAddress->name,
                    'company' => $shipment->toAddress->company,
                    'street1' => $shipment->toAddress->street1,
                    'street2' => $shipment->toAddress->street2,
                    'city' => $shipment->toAddress->city,
                    'state' => $shipment->toAddress->state,
                    'zip' => $shipment->toAddress->zip,
                    'country' => $shipment->toAddress->country,
                    'phone' => $shipment->toAddress->phone,
                    'email' => $shipment->toAddress->email
                ],
                'parcel' => [
                    'weight' => $shipment->weight,
                    'height' => $shipment->dimensions['height'] ?? null,
                    'width' => $shipment->dimensions['width'] ?? null,
                    'length' => $shipment->dimensions['length'] ?? null
                ]
            ]);

            // Etiketi satın al
            $this->client->shipment->buy($easyPostShipment->id, ['rate' => ['id' => $rate->id]]);

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
                'label_details' => [
                    'id' => $easyPostShipment->postage_label->id ?? '',
                    'label_url' => $easyPostShipment->postage_label->label_url ?? '',
                    'label_format' => $easyPostShipment->postage_label->label_format ?? '',
                    'label_size' => $easyPostShipment->postage_label->label_size ?? '',
                    'tracking_code' => $easyPostShipment->postage_label->tracking_code ?? ''
                ],
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
            $tracker = $this->client->tracker->create([
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
            
            // Check if it's a network connectivity issue
            if (strpos($e->getMessage(), 'Could not resolve host') !== false || 
                strpos($e->getMessage(), 'cURL error 6') !== false ||
                strpos($e->getMessage(), 'timeout') !== false) {
                throw new Exception('Network connectivity issue. Please check your internet connection and try again.');
            }
            
            throw new Exception('Kargo takip edilemedi: ' . $e->getMessage());
        }
    }

    /**
     * Kargo durumunu güncelleme
     */
    public function updateShipmentStatus(Shipment $shipment): void
    {
        try {
            $tracker = $this->client->tracker->retrieve($shipment->tracking_number);
            
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
            
            // Check if it's a network connectivity issue
            if (strpos($e->getMessage(), 'Could not resolve host') !== false || 
                strpos($e->getMessage(), 'cURL error 6') !== false ||
                strpos($e->getMessage(), 'timeout') !== false) {
                throw new Exception('Network connectivity issue. Please check your internet connection and try again.');
            }
            
            throw new Exception('Kargo durumu güncellenemedi: ' . $e->getMessage());
        }
    }

    /**
     * Kargo iptali
     */
    public function cancelShipment(Shipment $shipment): bool
    {
        try {
            $easyPostShipment = $this->client->shipment->retrieve($shipment->tracking_number);
            $refund = $this->client->shipment->refund($easyPostShipment->id);

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