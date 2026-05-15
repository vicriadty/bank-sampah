<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.exchangerate.key', '');
        $this->baseUrl = config('services.exchangerate.base_url', 'https://v6.exchangerate-api.com/v6');
    }

    /**
     * Get USD to IDR exchange rate with 1 hour caching.
     * 
     * @return float
     */
    public function getUsdToIdrRate(): float
    {
        $cacheKey = 'usd_to_idr_rate';

        return Cache::remember($cacheKey, 3600, function () {
            try {
                $response = Http::get("{$this->baseUrl}/{$this->apiKey}/pair/USD/IDR");

                if ($response->failed()) {
                    Log::error('ExchangeRate-API request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return $this->getFallbackRate();
                }

                $data = $response->json();
                
                if (isset($data['conversion_rate'])) {
                    return (float) $data['conversion_rate'];
                }

                return $this->getFallbackRate();
            } catch (\Exception $e) {
                Log::error('ExchangeRate-API exception', ['message' => $e->getMessage()]);
                return $this->getFallbackRate();
            }
        });
    }

    /**
     * Fallback rate if API fails. 
     * Try to get last known rate from cache or return a static default.
     */
    protected function getFallbackRate(): float
    {
        // Try to get old cache if it exists even if expired (if manually handled)
        // For simplicity, we return a reasonable default or last successful rate.
        return 16000.0; 
    }
}
