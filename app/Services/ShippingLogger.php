<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ShippingLogger
{
    protected $channel;
    protected $level;
    protected $enabled;

    public function __construct()
    {
        $this->channel = Config::get('shipping.logging.channel', 'daily');
        $this->level = Config::get('shipping.logging.level', 'info');
        $this->enabled = Config::get('shipping.logging.enabled', true);
    }

    /**
     * Log shipping label generation
     */
    public function logLabelGeneration($order, $result, $error = null)
    {
        if (!$this->shouldLog('label_generation')) {
            return;
        }

        $context = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'carrier' => $order->shippingCarrier->name,
            'service' => $order->shippingService->name,
            'tracking_number' => $order->shipping_tracking_number,
        ];

        if ($error) {
            $context['error'] = $error;
            $this->log('error', 'Failed to generate shipping label', $context);
        } else {
            $this->log('info', 'Shipping label generated successfully', $context);
        }
    }

    /**
     * Log shipping rate calculation
     */
    public function logRateCalculation($request, $result, $error = null)
    {
        if (!$this->shouldLog('rate_calculation')) {
            return;
        }

        $context = [
            'carrier_id' => $request->carrier_id,
            'service_id' => $request->service_id,
            'from_country' => $request->from_country,
            'to_country' => $request->to_country,
            'weight' => $request->weight,
        ];

        if ($error) {
            $context['error'] = $error;
            $this->log('error', 'Failed to calculate shipping rate', $context);
        } else {
            $context['rate'] = $result['cost'];
            $this->log('info', 'Shipping rate calculated successfully', $context);
        }
    }

    /**
     * Log tracking updates
     */
    public function logTrackingUpdate($shipment, $status, $details = [])
    {
        if (!$this->shouldLog('tracking_updates')) {
            return;
        }

        $context = [
            'shipment_id' => $shipment->id,
            'tracking_number' => $shipment->tracking_number,
            'old_status' => $shipment->status,
            'new_status' => $status,
            'details' => $details,
        ];

        $this->log('info', 'Shipment tracking updated', $context);
    }

    /**
     * Log API requests
     */
    public function logApiRequest($carrier, $endpoint, $request, $response, $error = null)
    {
        if (!$this->shouldLog('api_requests')) {
            return;
        }

        $context = [
            'carrier' => $carrier,
            'endpoint' => $endpoint,
            'request' => $request,
            'response' => $response,
        ];

        if ($error) {
            $context['error'] = $error;
            $this->log('error', 'API request failed', $context);
        } else {
            $this->log('info', 'API request successful', $context);
        }
    }

    /**
     * Log errors
     */
    public function logError($message, $context = [])
    {
        if (!$this->shouldLog('errors')) {
            return;
        }

        $this->log('error', $message, $context);
    }

    /**
     * Check if specific event should be logged
     */
    protected function shouldLog($event)
    {
        return $this->enabled && Config::get("shipping.logging.events.{$event}", true);
    }

    /**
     * Write log message
     */
    protected function log($level, $message, $context = [])
    {
        Log::channel($this->channel)->{$level}($message, $context);
    }
} 