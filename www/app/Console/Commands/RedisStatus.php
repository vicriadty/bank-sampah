<?php

namespace App\Console\Commands;

use App\Services\RedisService;
use Illuminate\Console\Command;

class RedisStatus extends Command
{
    protected $signature = 'redis:status';
    protected $description = 'Tampilkan status koneksi dan statistik Redis';

    public function handle(RedisService $redis): int
    {
        $this->info('Memeriksa koneksi Redis...');

        try {
            $info = $redis->info();

            if (empty($info)) {
                $this->error('Tidak dapat mengambil info Redis. Periksa koneksi.');
                return Command::FAILURE;
            }

            $this->newLine();
            $this->line(' Server Info');
            $this->line('   Version:     ' . $info['server_version']);
            $this->line('   Uptime:      ' . $info['uptime_in_seconds'] . ' detik');
            $this->newLine();

            $this->line(' Memory');
            $this->line('   Used:        ' . $info['used_memory_human']);
            $this->line('   Peak:        ' . $info['used_memory_peak_human']);
            $this->newLine();

            $this->line(' Clients');
            $this->line('   Connected:   ' . $info['connected_clients']);
            $this->newLine();

            $this->line(' Stats');
            $this->line('   Total Keys:  ' . $info['db_size']);
            $this->line('   Hits:        ' . $info['keyspace_hits']);
            $this->line('   Misses:      ' . $info['keyspace_misses']);

            $totalOps = $info['keyspace_hits'] + $info['keyspace_misses'];
            $hitRate = $totalOps > 0 ? round(($info['keyspace_hits'] / $totalOps) * 100, 2) : 0;
            $this->line('   Hit Rate:    ' . $hitRate . '%');

            $this->newLine();
            $this->info('Redis berjalan normal.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke Redis: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
