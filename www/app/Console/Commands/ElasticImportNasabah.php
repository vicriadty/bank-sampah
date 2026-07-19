<?php

namespace App\Console\Commands;

use App\Models\Nasabah;
use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticImportNasabah extends Command
{
    protected $signature = 'elastic:import:nasabah';
    protected $description = 'Import all nasabah records from MySQL to Elasticsearch';

    public function handle(ElasticsearchService $es): int
    {
        $index = 'nasabahs';

        if (!$es->indexExists($index)) {
            $this->warn("Index '{$index}' does not exist. Run 'elastic:index:create {$index}' first.");
            if (!$this->confirm('Create it now?')) {
                return Command::SUCCESS;
            }
            $this->call('elastic:index:create', ['index' => $index]);
        }

        $bar = $this->output->createProgressBar(
            Nasabah::count()
        );
        $bar->start();

        $chunkSize = 100;
        $totalSuccess = 0;
        $totalFailed = 0;

        Nasabah::chunk($chunkSize, function ($nasabahs) use ($es, $index, $bar, &$totalSuccess, &$totalFailed) {
            $documents = [];
            foreach ($nasabahs as $nasabah) {
                $documents[] = [
                    'id' => $nasabah->id,
                    'nik' => $nasabah->nik,
                    'nama' => $nasabah->nama,
                    'email' => $nasabah->email,
                    'alamat' => $nasabah->alamat,
                    'no_hp' => $nasabah->no_hp,
                    'created_at' => $nasabah->created_at?->toIso8601String(),
                ];
            }

            $result = $es->bulkIndex($index, $documents);
            $totalSuccess += $result['success'];
            $totalFailed += $result['failed'];
            $bar->advance(count($nasabahs));
        });

        $bar->finish();
        $this->newLine();
        $this->info("Imported: {$totalSuccess} documents, Failed: {$totalFailed}");

        return Command::SUCCESS;
    }
}
