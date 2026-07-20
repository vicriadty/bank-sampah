<?php

namespace App\Jobs;

use App\Models\DompetNasabah;
use App\Models\Pengaturan;
use App\Models\RiwayatKonversiEmas;
use App\Models\Setoran;
use App\Services\GoldPriceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConvertGoldBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const THRESHOLD_GRAM = 0.05;

    public int $timeout = 300;
    public int $tries = 1;

    public function handle(GoldPriceService $goldPriceService): void
    {
        $logChannel = 'gold-convert';

        $masterSwitch = Pengaturan::getValue('master_switch_auto_convert', '0');
        if ($masterSwitch !== '1') {
            Log::channel($logChannel)->warning('Auto-convert job dihentikan: Master Switch OFF');
            return;
        }

        $goldPrice = $goldPriceService->getPrice();
        $hargaEmasPerGram = (float) ($goldPrice['price_per_gram'] ?? 0);

        if ($hargaEmasPerGram <= 0) {
            Log::channel($logChannel)->error('Auto-convert job dibatalkan: Harga emas tidak tersedia');
            return;
        }

        $targetRupiah = self::THRESHOLD_GRAM * $hargaEmasPerGram;

        $totalNasabahDiproses = 0;
        $totalRupiahDikonversi = 0;
        $totalEmasDihasilkan = 0;

        DompetNasabah::where('saldo_rupiah', '>=', $targetRupiah)
            ->chunk(100, function ($dompets) use (
                $targetRupiah, $hargaEmasPerGram,
                &$totalNasabahDiproses, &$totalRupiahDikonversi, &$totalEmasDihasilkan, $logChannel
            ) {
                $nasabahIds = $dompets->pluck('nasabah_id');

                $setoranTodayTotals = Setoran::whereIn('nasabah_id', $nasabahIds)
                    ->whereDate('created_at', today())
                    ->where('status', 'berhasil')
                    ->groupBy('nasabah_id')
                    ->selectRaw('nasabah_id, sum(total_harga) as total')
                    ->pluck('total', 'nasabah_id');

                foreach ($dompets as $dompet) {
                    $saldoEfektif = $dompet->saldo_rupiah - ($setoranTodayTotals[$dompet->nasabah_id] ?? 0);

                    if ($saldoEfektif < $targetRupiah) continue;

                    $kelipatan = floor($saldoEfektif / $targetRupiah);
                    if ($kelipatan < 1) continue;

                    $jumlahRupiahKonversi = $kelipatan * $targetRupiah;
                    $jumlahGram = $kelipatan * self::THRESHOLD_GRAM;

                    DB::transaction(function () use ($dompet, $jumlahRupiahKonversi, $jumlahGram, $hargaEmasPerGram) {
                        $dompet->decrement('saldo_rupiah', $jumlahRupiahKonversi);
                        $dompet->increment('saldo_emas_gram', $jumlahGram);

                        RiwayatKonversiEmas::create([
                            'nasabah_id'          => $dompet->nasabah_id,
                            'saldo_terpakai'      => $jumlahRupiahKonversi,
                            'harga_emas_per_gram' => $hargaEmasPerGram,
                            'jumlah_gram'         => $jumlahGram,
                            'sisa_saldo_rupiah'   => $dompet->fresh()->saldo_rupiah,
                            'total_saldo_emas'    => $dompet->fresh()->saldo_emas_gram,
                        ]);
                    });

                    $totalNasabahDiproses++;
                    $totalRupiahDikonversi += $jumlahRupiahKonversi;
                    $totalEmasDihasilkan += $jumlahGram;
                }
            });

        Log::channel($logChannel)->info('Auto-convert job selesai', [
            'nasabah_diproses' => $totalNasabahDiproses,
            'rupiah_dikonversi' => $totalRupiahDikonversi,
            'emas_dihasilkan' => $totalEmasDihasilkan,
        ]);
    }
}
