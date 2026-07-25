<?php

namespace App\Services;

use App\Models\DetailPenjualanSampah;
use App\Models\DompetNasabah;
use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\Pengepul;
use App\Models\PenjualanSampah;
use App\Models\RiwayatKonversiEmas;
use App\Models\Setoran;
use App\Models\SetoranDetail;
use Illuminate\Support\Facades\DB;

class CacheService
{
    public function __construct(
        private RedisService $redis
    ) {}

    public function getDashboardSummary(): array
    {
        return $this->redis->remember('dashboard:summary', 600, function () {
            return [
                'jumlah_nasabah' => Nasabah::count(),
                'jumlah_pengepul' => Pengepul::count(),
                'jumlah_sampah' => JenisSampah::count(),
                'total_sampah_disetorkan' => SetoranDetail::sum('berat'),
                'total_tabungan_nasabah' => DompetNasabah::sum('saldo_rupiah'),
                'total_penjualan_sampah' => DetailPenjualanSampah::sum('berat'),
                'total_penjualan' => PenjualanSampah::sum('total_harga'),
            ];
        });
    }

    public function getGoldStats(): array
    {
        return $this->redis->remember('dashboard:gold-stats', 600, function () {
            return [
                'total_rupiah_dikonversi' => RiwayatKonversiEmas::sum('saldo_terpakai'),
                'total_gold_exchanged' => RiwayatKonversiEmas::sum('jumlah_gram'),
            ];
        });
    }

    public function getSetoranPerBulan(): mixed
    {
        return $this->redis->remember('dashboard:setoran-per-bulan', 600, function () {
            return Setoran::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('SUM(total_harga) as total')
            )
                ->whereYear('created_at', 2026)
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();
        });
    }

    public function getTransaksiPerBulan(): mixed
    {
        return $this->redis->remember('dashboard:transaksi-per-bulan', 600, function () {
            return SetoranDetail::join('setorans', 'setoran_details.setoran_id', '=', 'setorans.id')
                ->join('jenis_sampahs', 'setoran_details.sampah_id', '=', 'jenis_sampahs.id')
                ->join('kategori_sampahs', 'jenis_sampahs.kategori_id', '=', 'kategori_sampahs.id')
                ->select(
                    DB::raw("DATE_FORMAT(setoran_details.created_at, '%Y-%m') as bulan"),
                    'kategori_sampahs.nama_kategori as kategori',
                    DB::raw('SUM(setoran_details.berat) as total_berat'),
                    DB::raw('SUM(setoran_details.subtotal) as total_subtotal')
                )
                ->whereYear('setoran_details.created_at', '2026')
                ->groupBy('bulan', 'kategori_sampahs.nama_kategori')
                ->orderBy('bulan')
                ->get();
        });
    }

    public function getGoldPriceHistory(): mixed
    {
        return $this->redis->remember('dashboard:gold-price-history', 600, function () {
            return RiwayatKonversiEmas::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as tanggal"),
                DB::raw('AVG(harga_emas_per_gram) as harga_rata')
            )
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get();
        });
    }

    public function getNasabahBaruPerBulan(): mixed
    {
        return $this->redis->remember('dashboard:nasabah-baru-per-bulan', 600, function () {
            return Nasabah::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('COUNT(*) as total')
            )
                ->whereYear('created_at', 2026)
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();
        });
    }

    public function getKomposisiSampah(): mixed
    {
        return $this->redis->remember('dashboard:komposisi-sampah', 600, function () {
            return SetoranDetail::join('jenis_sampahs', 'setoran_details.sampah_id', '=', 'jenis_sampahs.id')
                ->join('kategori_sampahs', 'jenis_sampahs.kategori_id', '=', 'kategori_sampahs.id')
                ->select('kategori_sampahs.nama_kategori', DB::raw('SUM(setoran_details.berat) as total_berat'))
                ->groupBy('kategori_sampahs.nama_kategori')
                ->get();
        });
    }

    public function getGoldPerBulan(): mixed
    {
        return $this->redis->remember('dashboard:gold-per-bulan', 600, function () {
            return RiwayatKonversiEmas::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('SUM(jumlah_gram) as total_gram'),
                DB::raw('SUM(saldo_terpakai) as total_saldo')
            )
                ->whereYear('created_at', 2026)
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();
        });
    }

    public function getNasabahTerbaru(): mixed
    {
        return $this->redis->remember('dashboard:nasabah-terbaru', 600, function () {
            return Nasabah::latest()->take(5)->get();
        });
    }

    public function invalidateDashboard(): void
    {
        $keys = [
            'dashboard:summary',
            'dashboard:gold-stats',
            'dashboard:setoran-per-bulan',
            'dashboard:transaksi-per-bulan',
            'dashboard:nasabah-baru-per-bulan',
            'dashboard:komposisi-sampah',
            'dashboard:gold-per-bulan',
            'dashboard:gold-price-history',
            'dashboard:nasabah-terbaru',
        ];
        foreach ($keys as $key) {
            $this->redis->forget($key);
        }
    }

    public function invalidateGoldStats(): void
    {
        $this->redis->forget('dashboard:gold-stats');
        $this->redis->forget('dashboard:gold-per-bulan');
    }
}
