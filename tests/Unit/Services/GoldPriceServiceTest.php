<?php

namespace Tests\Unit\Services;

use App\Services\ExchangeRateService;
use App\Services\GoldPriceService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
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

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_get_price_converts_usd_rate_to_idr_and_gram_price(): void
    {
        $exchangeRateService = Mockery::mock(ExchangeRateService::class);
        $exchangeRateService->shouldReceive('getUsdToIdrRate')
            ->once()
            ->andReturn(16000.0);

        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => Http::response([
                'success' => true,
                'timestamp' => 1710000000,
                'rates' => [
                    'USDXAU' => 2000.0,
                ],
            ]),
        ]);

        $service = new GoldPriceService($exchangeRateService);
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

    public function test_get_price_uses_reciprocal_rate_when_usdxau_is_missing(): void
    {
        $exchangeRateService = Mockery::mock(ExchangeRateService::class);
        $exchangeRateService->shouldNotReceive('getUsdToIdrRate');

        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => Http::response([
                'success' => true,
                'timestamp' => 1710000000,
                'rates' => [
                    'XAU' => 0.0005,
                ],
            ]),
        ]);

        $service = new GoldPriceService($exchangeRateService);
        $price = $service->getPrice('XAU', 'USD');

        $expectedUsdPerOunce = 1 / 0.0005;
        $expectedUsdPerGram = $expectedUsdPerOunce / 28.3495;

        $this->assertSame('USD', $price['currency']);
        $this->assertEqualsWithDelta($expectedUsdPerOunce, $price['price_per_ounce'], 0.01);
        $this->assertEqualsWithDelta($expectedUsdPerGram, $price['price_per_gram'], 0.01);
    }

    public function test_get_price_returns_fallback_values_when_api_fails(): void
    {
        $exchangeRateService = Mockery::mock(ExchangeRateService::class);
        $exchangeRateService->shouldNotReceive('getUsdToIdrRate');

        Http::fake([
            'api.metalpriceapi.com/v1/latest*' => Http::response([
                'success' => false,
            ], 500),
        ]);

        $service = new GoldPriceService($exchangeRateService);
        $price = $service->getPrice('XAU', 'USD');

        $this->assertSame(0.0, $price['price_per_gram']);
        $this->assertSame(0.0, $price['price_per_ounce']);
        $this->assertSame('USD', $price['currency']);
        $this->assertNotEmpty($price['timestamp']);
    }
}
