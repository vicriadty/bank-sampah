<?php

namespace App\Console\Commands;

use App\Models\JenisSampah;
use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticImportSampah extends Command
{
    protected $signature = 'elastic:import:sampah';
    protected $description = 'Import all jenis sampah records from MySQL to Elasticsearch';

    public function handle(ElasticsearchService $es): int
    {
        $index = 'jenis_sampahs';

        if (!$es->indexExists($index)) {
            $this->warn("Index '{$index}' does not exist. Run 'elastic:index:create {$index}' first.");
            if (!$this->confirm('Create it now?')) {
                return Command::SUCCESS;
            }
            $this->call('elastic:index:create', ['index' => $index]);
        }

        $bar = $this->output->createProgressBar(
            JenisSampah::count()
        );
        $bar->start();

        $totalSuccess = 0;
        $totalFailed = 0;

        JenisSampah::with('kategoriSampah')->chunk(100, function ($sampahs) use ($es, $index, $bar, &$totalSuccess, &$totalFailed) {
            $documents = [];
            foreach ($sampahs as $sampah) {
                $documents[] = [
                    'id' => $sampah->id,
                    'nama_jenis' => $sampah->nama_jenis,
                    'kategori' => $sampah->kategoriSampah?->nama_kategori ?? '',
                    'harga_per_kg' => (float) $sampah->harga_per_kg,
                    'stok' => (float) $sampah->stok,
                ];
            }

            $result = $es->bulkIndex($index, $documents);
            $totalSuccess += $result['success'];
            $totalFailed += $result['failed'];
            $bar->advance(count($sampahs));
        });

        $bar->finish();
        $this->newLine();
        $this->info("Imported: {$totalSuccess} documents, Failed: {$totalFailed}");

        return Command::SUCCESS;
    }
}
