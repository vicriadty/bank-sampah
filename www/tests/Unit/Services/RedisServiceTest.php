<?php

namespace Tests\Unit\Services;

use App\Services\RedisService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RedisServiceTest extends TestCase
{
    private RedisService $redisService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        Cache::flush();
        $this->redisService = $this->app->make(RedisService::class);
    }

    public function test_put_and_get(): void
    {
        $this->redisService->put('test_key', 'test_value', 60);
        $this->assertEquals('test_value', $this->redisService->get('test_key'));
    }

    public function test_get_returns_default_on_miss(): void
    {
        $this->assertEquals('default_val', $this->redisService->get('nonexistent', 'default_val'));
    }

    public function test_remember(): void
    {
        $callCount = 0;
        $result1 = $this->redisService->remember('remember_key', 60, function () use (&$callCount) {
            $callCount++;
            return 'computed_' . $callCount;
        });
        $result2 = $this->redisService->remember('remember_key', 60, function () use (&$callCount) {
            $callCount++;
            return 'computed_' . $callCount;
        });

        $this->assertEquals('computed_1', $result1);
        $this->assertEquals('computed_1', $result2);
        $this->assertEquals(1, $callCount);
    }

    public function test_forget(): void
    {
        $this->redisService->put('forget_key', 'value', 60);
        $this->assertTrue($this->redisService->exists('forget_key'));
        $this->redisService->forget('forget_key');
        $this->assertFalse($this->redisService->exists('forget_key'));
    }

    public function test_increment_and_decrement(): void
    {
        Cache::put('counter', 0, 60);
        $this->redisService->increment('counter');
        $this->assertEquals(1, Cache::get('counter'));
        $this->redisService->increment('counter', 5);
        $this->assertEquals(6, Cache::get('counter'));
        $this->redisService->decrement('counter', 2);
        $this->assertEquals(4, Cache::get('counter'));
    }

    public function test_exists(): void
    {
        $this->assertFalse($this->redisService->exists('nonexistent'));
        Cache::put('exists_test', 'yes', 60);
        $this->assertTrue($this->redisService->exists('exists_test'));
    }
}
