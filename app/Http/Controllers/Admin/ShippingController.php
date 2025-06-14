<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShippingRate;
use App\Services\EasyPostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    protected $easyPostService;

    public function __construct(EasyPostService $easyPostService)
    {
        $this->easyPostService = $easyPostService;
    }

    /**
     * Kargo listesini görüntüle
     */
    public function index()
    {
        $shipments = Shipment::with(['fromAddress', 'toAddress', 'rate', 'label', 'tracker'])
            ->latest()
            ->paginate(20);

        return view('admin.shipping.index', compact('shipments'));
    }

    /**
     * Yeni kargo oluşturma formu
     */
    public function create()
    {
        return view('admin.shipping.create');
    }

    /** 
     * Kargo oluştur
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'from_address' => 'required|array',
                'to_address' => 'required|array',
                'weight' => 'required|numeric',
                'dimensions' => 'required|array',
                'dimensions.height' => 'required|numeric',
                'dimensions.width' => 'required|numeric',
                'dimensions.length' => 'required|numeric',
            ]);

            // Adresleri doğrula
            $fromAddress = $this->easyPostService->verifyAddress($validated['from_address']);
            $toAddress = $this->easyPostService->verifyAddress($validated['to_address']);

            // Kargo oluştur
            $shipment = Shipment::create([
                'from_address_id' => $fromAddress->id,
                'to_address_id' => $toAddress->id,
                'tracking_number' => 'TEMP_' . uniqid(),
                'weight' => $validated['weight'],
                'dimensions' => $validated['dimensions'],
                'total_price' => 0.00,
                'status' => 'pending'
            ]);

            // Fiyatları hesapla
            $rates = $this->easyPostService->calculateRates($shipment);

            return response()->json([
                'success' => true,
                'shipment' => $shipment->load(['fromAddress', 'toAddress']),
                'rates' => $rates
            ]);
        } catch (\Exception $e) {
            Log::error('Kargo oluşturma hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Kargo oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kargo detaylarını görüntüle
     */
    public function show(Shipment $shipment)
    {
        $shipment->load(['fromAddress', 'toAddress', 'rate', 'label', 'tracker']);
        return view('admin.shipping.show', compact('shipment'));
    }

    /**
     * Kargo etiketi oluştur
     */
    public function createLabel(Request $request, Shipment $shipment)
    {
        try {
            $validated = $request->validate([
                'rate_id' => 'required|exists:shipping_rates,id'
            ]);

            $rate = ShippingRate::findOrFail($validated['rate_id']);
            $label = $this->easyPostService->createLabel($shipment, $rate);

            return response()->json([
                'success' => true,
                'label' => $label
            ]);
        } catch (\Exception $e) {
            Log::error('Etiket oluşturma hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Etiket oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kargo durumunu güncelle
     */
    public function updateStatus(Shipment $shipment)
    {
        try {
            $this->easyPostService->updateShipmentStatus($shipment);
            
            return response()->json([
                'success' => true,
                'shipment' => $shipment->fresh(['tracker'])
            ]);
        } catch (\Exception $e) {
            Log::error('Durum güncelleme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Durum güncellenemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kargoyu iptal et
     */
    public function cancel(Shipment $shipment)
    {
        try {
            $result = $this->easyPostService->cancelShipment($shipment);
            
            return response()->json([
                'success' => $result,
                'message' => $result ? 'Kargo başarıyla iptal edildi' : 'Kargo iptal edilemedi'
            ]);
        } catch (\Exception $e) {
            Log::error('Kargo iptal hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Kargo iptal edilemedi: ' . $e->getMessage()
            ], 500);
        }
    }
} 