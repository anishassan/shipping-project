<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
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
     * Kargo takip sayfası
     */
    public function track()
    {
        return view('customer.shipping.track');
    }

    /**
     * Kargo durumu sorgula
     */
    public function trackShipment(Request $request)
    {
        try {
            $validated = $request->validate([
                'tracking_number' => 'required|string'
            ]);

            $shipment = Shipment::where('tracking_number', $validated['tracking_number'])
                ->firstOrFail();

            $this->easyPostService->updateShipmentStatus($shipment);

            return response()->json([
                'success' => true,
                'shipment' => $shipment->fresh(['tracker'])
            ]);
        } catch (\Exception $e) {
            Log::error('Kargo takip hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Kargo bulunamadı veya takip edilemedi'
            ], 404);
        }
    }

    /**
     * Kargo geçmişi
     */
    public function history()
    {
        $shipments = auth()->user()->shipments()
            ->with(['fromAddress', 'toAddress', 'rate', 'label', 'tracker'])
            ->latest()
            ->paginate(10);

        return view('customer.shipping.history', compact('shipments'));
    }

    /**
     * Kargo detayları
     */
    public function show(Shipment $shipment)
    {
        // Kullanıcının kendi kargosunu görüntülemesini sağla
        if ($shipment->user_id !== auth()->id()) {
            abort(403);
        }

        $shipment->load(['fromAddress', 'toAddress', 'rate', 'label', 'tracker']);
        return view('customer.shipping.show', compact('shipment'));
    }

    /**
     * Kargo etiketini indir
     */
    public function downloadLabel(Shipment $shipment)
    {
        // Kullanıcının kendi kargo etiketini indirmesini sağla
        if ($shipment->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$shipment->label) {
            return back()->with('error', 'Kargo etiketi bulunamadı');
        }

        return response()->download(
            storage_path('app/' . $shipment->label->label_file_path),
            'shipping-label.pdf'
        );
    }
} 