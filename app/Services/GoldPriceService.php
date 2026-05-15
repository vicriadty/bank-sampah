<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    private const OUNCE_TO_GRAM = 28.3495;

    protected string $apiKey;
    protected string $baseUrl;
    protected string $defaultMetal;
    protected string $defaultCurrency;
    protected ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->apiKey = config('services.metalpriceapi.key', '');
        $this->baseUrl = rtrim(config('services.metalpriceapi.base_url', 'https://api.metalpriceapi.com/v1'), '/');
        $this->defaultMetal = config('services.metalpriceapi.default_metal', 'XAU');
        $this->defaultCurrency = config('services.metalpriceapi.default_currency', 'IDR');
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Get gold price from MetalpriceAPI with conversion and caching.
     *
     * @return array{price_per_gram: float, price_per_ounce: float, currency: string, timestamp: string}
     */
    public function getPrice(?string $metal = null, ?string $currency = null): array
    {
        $metal = strtoupper($metal ?? $this->defaultMetal);
        $currency = strtoupper($currency ?? $this->defaultCurrency);

        $cacheKey = "gold_price_{$metal}_{$currency}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($metal, $currency) {
            return $this->fetchWithConversion($metal, $currency);
        });
    }

    /**
     * Fetch from API and handle USD to IDR conversion if needed.
     */
    protected function fetchWithConversion(string $metal, string $currency): array
    {
        $basePriceData = $this->fetchFromApi($metal);

        if ($currency !== 'IDR') {
            $basePriceData['currency'] = 'USD';
            return $basePriceData;
        }

        if ($basePriceData['price_per_gram'] <= 0) {
            $basePriceData['currency'] = 'IDR';
            return $basePriceData;
        }

        $rate = $this->exchangeRateService->getUsdToIdrRate();

        $basePriceData['price_per_gram'] *= $rate;
        $basePriceData['price_per_ounce'] *= $rate;
        $basePriceData['currency'] = 'IDR';

        return $basePriceData;
    }

    /**
     * Fetch price directly from MetalpriceAPI.
     */
    protected function fetchFromApi(string $metal): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/latest", [
                'api_key' => $this->apiKey,
                'base' => 'USD',
                'currencies' => $metal,
            ]);

            if ($response->failed()) {
                Log::error('MetalpriceAPI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->fallbackPrice('USD');
            }

            $data = $response->json() ?: [];

            if (array_key_exists('success', $data) && $data['success'] === false) {
                Log::error('MetalpriceAPI returned an error payload', [
                    'body' => $data,
                ]);

                return $this->fallbackPrice('USD');
            }

            $rates = $data['rates'] ?? [];
            $timestamp = isset($data['timestamp'])
                ? CarbonImmutable::createFromTimestampUTC((int) $data['timestamp'])->toIso8601String()
                : now()->toIso8601String();

            $pricePerOunceUsd = $this->extractPricePerOunce($rates, $metal);

            if ($pricePerOunceUsd <= 0) {
                Log::warning('MetalpriceAPI response did not contain a valid gold rate', [
                    'metal' => $metal,
                    'response_keys' => array_keys($rates),
                ]);

                return $this->fallbackPrice('USD');
            }

            $pricePerGramUsd = $pricePerOunceUsd / self::OUNCE_TO_GRAM;

            return [
                'price_per_gram' => $pricePerGramUsd,
                'price_per_ounce' => $pricePerOunceUsd,
                'currency' => 'USD',
                'timestamp' => $timestamp,
            ];
        } catch (\Exception $e) {
            Log::error('MetalpriceAPI request exception', ['message' => $e->getMessage()]);

            return $this->fallbackPrice('USD');
        }
    }

    /**
     * Return a fallback structure when API is unavailable.
     */
    protected function fallbackPrice(string $currency): array
    {
        return [
            'price_per_gram' => 0.0,
            'price_per_ounce' => 0.0,
            'currency' => $currency,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Extract the gold price per ounce from a MetalpriceAPI response.
     */
    protected function extractPricePerOunce(array $rates, string $metal): float
    {
        $directKey = 'USD' . $metal;

        if (isset($rates[$directKey]) && is_numeric($rates[$directKey])) {
            return (float) $rates[$directKey];
        }

        if (isset($rates[$metal]) && is_numeric($rates[$metal]) && (float) $rates[$metal] > 0) {
            return 1 / (float) $rates[$metal];
        }

        return 0.0;
    }

    /**
     * Clear cached gold price.
     */
    public function clearCache(?string $metal = null, ?string $currency = null): void
    {
        $metal = strtoupper($metal ?? $this->defaultMetal);
        $currency = strtoupper($currency ?? $this->defaultCurrency);
        Cache::forget("gold_price_{$metal}_{$currency}");
    }
}
