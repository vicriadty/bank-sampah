<?php

namespace Tests\Unit\Services;

use App\Services\GoldPriceService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoldPriceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'services.metalpriceapi.key' => 'test-metalprice-key',
            'services.metalpriceapi.base_url' => 'https://api.metalpriceapi.com/v1',
            'services.metalpriceapi.default_metal' => 'XAU',
            'services.metalpriceapi.default_currency' => 'IDR',
        ]);

        Cache::flush();
        Http::preventStrayRequests();
    }

    public function test_get_price_converts_usd_rate_to_idr_and_gram_price(): void
    {
        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => Http::response([
                'success' => true,
                'timestamp' => 1710000000,
                'rates' => [
                    'USDXAU' => 2000.0,
                    'IDR' => 16000.0,
                ],
            ]),
        ]);

        $service = $this->app->make(GoldPriceService::class);
        $price = $service->getPrice();

        $expectedUsdPerOunce = 2000.0;
        $expectedUsdPerGram = $expectedUsdPerOunce / 28.3495;
        $expectedIdrPerOunce = $expectedUsdPerOunce * 16000.0;
        $expectedIdrPerGram = $expectedUsdPerGram * 16000.0;

        $this->assertSame('IDR', $price['currency']);
        $this->assertEqualsWithDelta($expectedIdrPerOunce, $price['price_per_ounce'], 0.01);
        $this->assertEqualsWithDelta($expectedIdrPerGram, $price['price_per_gram'], 0.01);
        $this->assertSame(
            CarbonImmutable::createFromTimestampUTC(1710000000)->toIso8601String(),
            $price['timestamp']
        );
    }

    public function test_get_price_uses_usdidr_reciprocal_when_idr_key_is_missing(): void
    {
        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => Http::response([
                'success' => true,
                'timestamp' => 1710000000,
                'rates' => [
                    'USDXAU' => 2000.0,
                    'USDIDR' => 0.0000625,
                ],
            ]),
        ]);

        $service = $this->app->make(GoldPriceService::class);
        $price = $service->getPrice();

        $expectedIdrRate = 1 / 0.0000625;
        $expectedIdrPerOunce = 2000.0 * $expectedIdrRate;

        $this->assertSame('IDR', $price['currency']);
        $this->assertEqualsWithDelta($expectedIdrPerOunce, $price['price_per_ounce'], 0.01);
    }

    public function test_get_price_uses_reciprocal_rate_when_usdxau_is_missing(): void
    {
        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => Http::response([
                'success' => true,
                'timestamp' => 1710000000,
                'rates' => [
                    'XAU' => 0.0005,
                ],
            ]),
        ]);

        $service = $this->app->make(GoldPriceService::class);
        $price = $service->getPrice('XAU', 'USD');

        $expectedUsdPerOunce = 1 / 0.0005;
        $expectedUsdPerGram = $expectedUsdPerOunce / 28.3495;

        $this->assertSame('USD', $price['currency']);
        $this->assertEqualsWithDelta($expectedUsdPerOunce, $price['price_per_ounce'], 0.01);
        $this->assertEqualsWithDelta($expectedUsdPerGram, $price['price_per_gram'], 0.01);
    }

    public function test_get_price_returns_fallback_values_when_api_fails(): void
    {
        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => Http::response([
                'success' => false,
            ], 500),
        ]);

        $service = $this->app->make(GoldPriceService::class);
        $price = $service->getPrice('XAU', 'USD');

        $this->assertSame(0.0, $price['price_per_gram']);
        $this->assertSame(0.0, $price['price_per_ounce']);
        $this->assertSame('USD', $price['currency']);
        $this->assertNotEmpty($price['timestamp']);
    }

    public function test_get_price_returns_idr_fallback_when_idr_rate_missing(): void
    {
        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => Http::response([
                'success' => true,
                'timestamp' => 1710000000,
                'rates' => [
                    'USDXAU' => 2000.0,
                ],
            ]),
        ]);

        $service = $this->app->make(GoldPriceService::class);
        $price = $service->getPrice();

        $this->assertSame('USD', $price['currency']);
        $this->assertGreaterThan(0, $price['price_per_gram']);
    }

    public function test_cache_serves_subsequent_requests(): void
    {
        $callCount = 0;

        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => function () use (&$callCount) {
                $callCount++;
                return Http::response([
                    'success' => true,
                    'timestamp' => 1710000000,
                    'rates' => [
                        'USDXAU' => 2000.0,
                        'IDR' => 16000.0,
                    ],
                ]);
            },
        ]);

        $service = $this->app->make(GoldPriceService::class);

        $price1 = $service->getPrice();
        $price2 = $service->getPrice();
        $price3 = $service->getPrice();

        $this->assertSame(1, $callCount);
        $this->assertSame($price1['price_per_gram'], $price2['price_per_gram']);
        $this->assertSame($price1['price_per_gram'], $price3['price_per_gram']);
    }
}
