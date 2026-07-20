<?php

namespace App\Console\Commands;

use App\Services\RedisService;
use Illuminate\Console\Command;

class RedisFlushCache extends Command
{
    protected $signature = 'redis:flush-cache';
    protected $description = 'Hapus semua cache Redis (flushdb)';

    public function handle(RedisService $redis): int
    {
        if ($this->confirm('Yakin ingin menghapus semua cache Redis?', true)) {
            $redis->flush();
            $this->info('Semua cache Redis berhasil dihapus.');
            return Command::SUCCESS;
        }

        $this->warn('Dibatalkan.');
        return Command::SUCCESS;
    }
}
