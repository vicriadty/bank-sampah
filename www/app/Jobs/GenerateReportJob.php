<?php

namespace App\Jobs;

use App\Models\Nasabah;
use App\Models\PenjualanSampah;
use App\Models\Setoran;
use App\Services\CacheService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(
        private string $type,
        private array $params = []
    ) {}

    public function handle(CacheService $cacheService): void
    {
        Log::info('GenerateReportJob started', ['type' => $this->type, 'params' => $this->params]);

        try {
            switch ($this->type) {
                case 'nasabah':
                    $this->generateNasabahReport($cacheService);
                    break;
                case 'setoran':
                    $this->generateSetoranReport($cacheService);
                    break;
                case 'penjualan':
                    $this->generatePenjualanReport($cacheService);
                    break;
                default:
                    Log::warning('Unknown report type', ['type' => $this->type]);
            }
        } catch (\Exception $e) {
            Log::error('GenerateReportJob failed', [
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function generateNasabahReport(CacheService $cacheService): void
    {
        $nasabah = Nasabah::with('dompet')->get();
        $pdf = Pdf::loadView('admin.nasabah.laporan_pdf', compact('nasabah'));
        $path = 'reports/nasabah-' . now()->format('Y-m-d-His') . '.pdf';
        Storage::put($path, $pdf->output());
        Log::info('Nasabah report generated', ['path' => $path]);
    }

    private function generateSetoranReport(CacheService $cacheService): void
    {
        $setoran = Setoran::with('nasabah', 'details')->get();
        $pdf = Pdf::loadView('admin.transaksi.setor-sampah.laporan_pdf', compact('setoran'));
        $path = 'reports/setoran-' . now()->format('Y-m-d-His') . '.pdf';
        Storage::put($path, $pdf->output());
        Log::info('Setoran report generated', ['path' => $path]);
    }

    private function generatePenjualanReport(CacheService $cacheService): void
    {
        $penjualan = PenjualanSampah::with('details', 'pengepul')->get();
        $pdf = Pdf::loadView('admin.transaksi.penjualan-sampah.laporan_pdf', compact('penjualan'));
        $path = 'reports/penjualan-' . now()->format('Y-m-d-His') . '.pdf';
        Storage::put($path, $pdf->output());
        Log::info('Penjualan report generated', ['path' => $path]);
    }
}
