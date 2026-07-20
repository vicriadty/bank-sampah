<?php

namespace App\Console\Commands;

use App\Jobs\ConvertGoldBalanceJob;
use Illuminate\Console\Command;

class GoldAutoConvert extends Command
{
    protected $signature = 'gold:auto-convert';
    protected $description = 'Konversi saldo rupiah nasabah ke emas secara otomatis (via job)';

    public function handle(): int
    {
        $this->info('Menjalankan ConvertGoldBalanceJob...');
        ConvertGoldBalanceJob::dispatch();
        $this->info('Job berhasil didispatch.');
        return Command::SUCCESS;
    }
}
