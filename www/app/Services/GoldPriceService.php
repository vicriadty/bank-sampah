<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    private const OUNCE_TO_GRAM = 28.3495;
    private const CACHE_TTL = 1800;

    protected string $apiKey;
    protected string $baseUrl;
    protected string $defaultMetal;
    protected string $defaultCurrency;

    public function __construct(
        private RedisService $redis
    ) {
        $this->apiKey = config('services.metalpriceapi.key', '');
        $this->baseUrl = rtrim(config('services.metalpriceapi.base_url', 'https://api.metalpriceapi.com/v1'), '/');
        $this->defaultMetal = config('services.metalpriceapi.default_metal', 'XAU');
        $this->defaultCurrency = config('services.metalpriceapi.default_currency', 'IDR');
    }

    public function getPrice(?string $metal = null, ?string $currency = null): array
    {
        $metal = strtoupper($metal ?? $this->defaultMetal);
        $currency = strtoupper($currency ?? $this->defaultCurrency);

        $cacheKey = "gold_price_{$metal}_{$currency}";

        return $this->redis->remember($cacheKey, self::CACHE_TTL, function () use ($metal, $currency) {
            return $this->fetchPrice($metal, $currency);
        });
    }

    protected function fetchPrice(string $metal, string $currency): array
    {
        try {
            $currenciesParam = $metal;
            if ($currency === 'IDR') {
                $currenciesParam .= ',IDR';
            }

            $response = Http::get("{$this->baseUrl}/latest", [
                'api_key' => $this->apiKey,
                'base' => 'USD',
                'currencies' => $currenciesParam,
            ]);

            if ($response->failed()) {
                Log::error('MetalpriceAPI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $this->fallbackPrice($currency);
            }

            $data = $response->json() ?: [];

            if (array_key_exists('success', $data) && $data['success'] === false) {
                Log::error('MetalpriceAPI returned an error payload', [
                    'body' => $data,
                ]);
                return $this->fallbackPrice($currency);
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
                return $this->fallbackPrice($currency);
            }

            $multiplier = 1.0;
            $targetCurrency = 'USD';

            if ($currency === 'IDR') {
                $idrRate = $this->extractFiatRate($rates, 'IDR');
                if ($idrRate > 0) {
                    $multiplier = $idrRate;
                    $targetCurrency = 'IDR';
                }
            }

            $pricePerGramUsd = $pricePerOunceUsd / self::OUNCE_TO_GRAM;

            return [
                'price_per_gram' => $pricePerGramUsd * $multiplier,
                'price_per_ounce' => $pricePerOunceUsd * $multiplier,
                'currency' => $targetCurrency,
                'timestamp' => $timestamp,
            ];
        } catch (\Exception $e) {
            Log::error('MetalpriceAPI request exception', ['message' => $e->getMessage()]);
            return $this->fallbackPrice($currency);
        }
    }

    protected function fallbackPrice(string $currency): array
    {
        return [
            'price_per_gram' => 0.0,
            'price_per_ounce' => 0.0,
            'currency' => $currency,
            'timestamp' => now()->toIso8601String(),
        ];
    }

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

    protected function extractFiatRate(array $rates, string $code): float
    {
        if (isset($rates[$code]) && is_numeric($rates[$code]) && (float) $rates[$code] > 0) {
            return (float) $rates[$code];
        }

        $prefixKey = 'USD' . $code;

        if (isset($rates[$prefixKey]) && is_numeric($rates[$prefixKey]) && (float) $rates[$prefixKey] > 0) {
            return 1 / (float) $rates[$prefixKey];
        }

        return 0.0;
    }

    public function clearCache(?string $metal = null, ?string $currency = null): void
    {
        $metal = strtoupper($metal ?? $this->defaultMetal);
        $currency = strtoupper($currency ?? $this->defaultCurrency);
        $this->redis->forget("gold_price_{$metal}_{$currency}");
    }
}
