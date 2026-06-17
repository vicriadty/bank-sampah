<?php

namespace App\Console\Commands;

use App\Models\DompetNasabah;
use App\Models\Pengaturan;
use App\Models\RiwayatKonversiEmas;
use App\Models\Setoran;
use App\Services\GoldPriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoldAutoConvert extends Command
{
    protected $signature = 'gold:auto-convert';
    protected $description = 'Konversi saldo rupiah nasabah ke emas secara otomatis berdasarkan aturan ambang batas, kelipatan, dan holding';

    // Threshold dalam gram emas — minimal 0.5 gram untuk bisa dikonversi
    private const THRESHOLD_GRAM = 0.5;

    public function handle(GoldPriceService $goldPriceService): int
    {
        // Langkah 1: Cek Master Switch — jika OFF, hentikan proses
        $masterSwitch = Pengaturan::getValue('master_switch_auto_convert', '0');
        if ($masterSwitch !== '1') {
            $this->warn('Master Switch OFF — proses auto-convert dihentikan.');
            return Command::SUCCESS;
        }

        // Langkah 2: Ambil harga emas hari ini dalam IDR
        $goldPrice = $goldPriceService->getPrice();
        $hargaEmasPerGram = (float) ($goldPrice['price_per_gram'] ?? 0);

        if ($hargaEmasPerGram <= 0) {
            $this->error('Harga emas tidak tersedia — proses dibatalkan.');
            return Command::FAILURE;
        }

        $this->info("Harga emas hari ini: Rp " . number_format($hargaEmasPerGram, 2, ',', '.') . " / gram");

        // Langkah 3: Hitung Target Rupiah (threshold 0.5 gram)
        $targetRupiah = self::THRESHOLD_GRAM * $hargaEmasPerGram;

        $this->info("Target rupiah (0.5 gram): Rp " . number_format($targetRupiah, 2, ',', '.') . "");

        // Statistik untuk log summary
        $totalNasabahDiproses = 0;
        $totalRupiahDikonversi = 0;
        $totalEmasDihasilkan = 0;

        // Langkah 4: Proses batch dengan chunk — hindari memory leak
        DompetNasabah::where('saldo_rupiah', '>=', $targetRupiah)
            ->chunk(100, function ($dompets) use (
                $targetRupiah,
                $hargaEmasPerGram,
                &$totalNasabahDiproses,
                &$totalRupiahDikonversi,
                &$totalEmasDihasilkan
            ) {
                // Langkah 5a: Kumpulkan total setoran hari ini per nasabah dalam chunk
                $nasabahIds = $dompets->pluck('nasabah_id');

                $setoranTodayTotals = Setoran::whereIn('nasabah_id', $nasabahIds)
                    ->whereDate('created_at', today())
                    ->where('status', 'berhasil')
                    ->groupBy('nasabah_id')
                    ->selectRaw('nasabah_id, sum(total_harga) as total')
                    ->pluck('total', 'nasabah_id');

                // Langkah 5b: Proses setiap dompet dalam chunk
                foreach ($dompets as $dompet) {
                    $this->prosesDompet(
                        $dompet,
                        $targetRupiah,
                        $hargaEmasPerGram,
                        $setoranTodayTotals[$dompet->nasabah_id] ?? 0,
                        $totalNasabahDiproses,
                        $totalRupiahDikonversi,
                        $totalEmasDihasilkan
                    );
                }
            });

        // Langkah 6: Log summary
        $this->info("Proses selesai.");
        $this->info("- Nasabah diproses: {$totalNasabahDiproses}");
        $this->info("- Rupiah dikonversi: Rp " . number_format($totalRupiahDikonversi, 2, ',', '.'));
        $this->info("- Emas dihasilkan: " . number_format($totalEmasDihasilkan, 4, ',', '.') . " gram");

        Log::info('Auto-convert gold selesai', [
            'nasabah_diproses' => $totalNasabahDiproses,
            'rupiah_dikonversi' => $totalRupiahDikonversi,
            'emas_dihasilkan' => $totalEmasDihasilkan,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Proses konversi untuk satu dompet nasabah.
     * Menerapkan Rule of Holding, Rule of Multiplier, lalu menjalankan transaksi.
     */
    private function prosesDompet(
        DompetNasabah $dompet,
        float $targetRupiah,
        float $hargaEmasPerGram,
        float $totalSetoranHariIni,
        int &$totalNasabahDiproses,
        float &$totalRupiahDikonversi,
        float &$totalEmasDihasilkan
    ): void {
        // Rule of Holding: kurangi saldo dengan setoran hari ini
        $saldoEfektif = $dompet->saldo_rupiah - $totalSetoranHariIni;

        if ($saldoEfektif < $targetRupiah) {
            return; // Tidak memenuhi threshold setelah holding adjustment
        }

        // Rule of Multiplier: hitung kelipatan 0.5 gram (pembulatan ke bawah)
        $kelipatan = floor($saldoEfektif / $targetRupiah);

        if ($kelipatan < 1) {
            return;
        }

        $jumlahRupiahKonversi = $kelipatan * $targetRupiah;
        $jumlahGram = $kelipatan * self::THRESHOLD_GRAM;

        // Jalankan transaksi database — keamanan finansial
        DB::transaction(function () use ($dompet, $jumlahRupiahKonversi, $jumlahGram, $hargaEmasPerGram) {
            // Kurangi saldo rupiah
            $dompet->decrement('saldo_rupiah', $jumlahRupiahKonversi);

            // Tambah saldo emas
            $dompet->increment('saldo_emas_gram', $jumlahGram);

            // Catat riwayat konversi
            RiwayatKonversiEmas::create([
                'nasabah_id'         => $dompet->nasabah_id,
                'saldo_terpakai'     => $jumlahRupiahKonversi,
                'harga_emas_per_gram' => $hargaEmasPerGram,
                'jumlah_gram'        => $jumlahGram,
                'sisa_saldo_rupiah'  => $dompet->fresh()->saldo_rupiah,
                'total_saldo_emas'   => $dompet->fresh()->saldo_emas_gram,
            ]);
        });

        // Update statistik
        $totalNasabahDiproses++;
        $totalRupiahDikonversi += $jumlahRupiahKonversi;
        $totalEmasDihasilkan += $jumlahGram;

        $this->line("  [OK] Nasabah #{$dompet->nasabah_id}: Rp " . number_format($jumlahRupiahKonversi, 0, ',', '.') .
            " -> " . number_format($jumlahGram, 4, ',', '.') . " gram");
    }
}
