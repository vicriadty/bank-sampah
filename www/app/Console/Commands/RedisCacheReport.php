<?php

namespace App\Console\Commands;

use App\Services\RedisService;
use Illuminate\Console\Command;

class RedisCacheReport extends Command
{
    protected $signature = 'redis:cache-report';
    protected $description = 'Tampilkan laporan keys cache Redis';

    public function handle(RedisService $redis): int
    {
        $this->info('Mengambil daftar cache keys...');

        $keys = $redis->keys('*');

        if (empty($keys)) {
            $this->warn('Tidak ada keys di Redis.');
            return Command::SUCCESS;
        }

        $info = $redis->info();

        $this->newLine();
        $this->line(" Total Keys: " . count($keys));
        $this->line(" Memory Used: " . ($info['used_memory_human'] ?? 'N/A'));
        $this->newLine();

        $grouped = [];
        foreach ($keys as $key) {
            $prefix = explode(':', $key)[0] ?? 'other';
            $grouped[$prefix][] = $key;
        }

        $this->line(" Keys by Prefix:");
        foreach ($grouped as $prefix => $groupKeys) {
            $this->line("   {$prefix}: " . count($groupKeys) . " keys");
        }

        $this->newLine();
        $this->line(" Detail Keys:");
        foreach ($grouped as $prefix => $groupKeys) {
            $this->line("   [{$prefix}]");
            foreach ($groupKeys as $key) {
                $this->line("     - {$key}");
            }
        }

        return Command::SUCCESS;
    }
}
