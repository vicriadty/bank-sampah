<?php

namespace App\Console\Commands;

use App\Models\Setoran;
use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticImportSetoran extends Command
{
    protected $signature = 'elastic:import:setoran';
    protected $description = 'Import all setoran records from MySQL to Elasticsearch';

    public function handle(ElasticsearchService $es): int
    {
        $index = 'setorans';

        if (!$es->indexExists($index)) {
            $this->warn("Index '{$index}' does not exist. Run 'elastic:index:create {$index}' first.");
            if (!$this->confirm('Create it now?')) {
                return Command::SUCCESS;
            }
            $this->call('elastic:index:create', ['index' => $index]);
        }

        $bar = $this->output->createProgressBar(
            Setoran::count()
        );
        $bar->start();

        $totalSuccess = 0;
        $totalFailed = 0;

        Setoran::with('nasabah')->chunk(100, function ($setorans) use ($es, $index, $bar, &$totalSuccess, &$totalFailed) {
            $documents = [];
            foreach ($setorans as $setoran) {
                $documents[] = [
                    'id' => $setoran->id,
                    'kode_setoran' => $setoran->kode_setoran,
                    'nasabah_id' => $setoran->nasabah_id,
                    'nasabah' => $setoran->nasabah?->nama ?? '',
                    'total_harga' => (float) $setoran->total_harga,
                    'status' => $setoran->status,
                    'created_at' => $setoran->created_at?->toIso8601String(),
                ];
            }

            $result = $es->bulkIndex($index, $documents);
            $totalSuccess += $result['success'];
            $totalFailed += $result['failed'];
            $bar->advance(count($setorans));
        });

        $bar->finish();
        $this->newLine();
        $this->info("Imported: {$totalSuccess} documents, Failed: {$totalFailed}");

        return Command::SUCCESS;
    }
}
