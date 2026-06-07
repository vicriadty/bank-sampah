<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\GoldExchange;
use App\Services\GoldPriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Nasabah;

class GoldExchangeController extends Controller
{
    public function index()
    {
        $nasabah = Auth::user()->nasabah;
        $goldExchanges = GoldExchange::where('nasabah_id', $nasabah->id)
            ->latest()
            ->paginate(10);

        return view('nasabah.gold-exchange.index', compact('nasabah', 'goldExchanges'));
    }

    public function create(GoldPriceService $goldPriceService)
    {
        $nasabah = Auth::user()->nasabah;
        $goldPrice = $goldPriceService->getPrice();

        return view('nasabah.gold-exchange.create', compact('nasabah', 'goldPrice'));
    }

    public function store(Request $request, GoldPriceService $goldPriceService)
    {
        $nasabah = Auth::user()->nasabah;

        $dompet = $nasabah->dompet;

        $request->validate([
            'jumlah_saldo' => 'required|numeric|min:10000|max:' . ($dompet->saldo_rupiah ?? 0),
        ], [
            'jumlah_saldo.max' => 'Saldo tidak mencukupi untuk jumlah penukaran ini.',
            'jumlah_saldo.min' => 'Minimal penukaran adalah Rp 10.000.',
        ]);

        $goldPrice = $goldPriceService->getPrice();

        if ($goldPrice['price_per_gram'] <= 0) {
            return back()->with('error', 'Harga emas tidak tersedia saat ini. Silakan coba lagi nanti.');
        }

        $jumlahSaldo = $request->jumlah_saldo;
        $hargaPerGram = $goldPrice['price_per_gram'];
        $jumlahGram = $jumlahSaldo / $hargaPerGram;

        DB::transaction(function () use ($nasabah, $dompet, $jumlahSaldo, $hargaPerGram, $jumlahGram, $request) {
            // Auto-convert: kurangi saldo rupiah, tambah saldo emas
            $dompet->decrement('saldo_rupiah', $jumlahSaldo);
            $dompet->increment('saldo_emas_gram', $jumlahGram);

            GoldExchange::create([
                'nasabah_id' => $nasabah->id,
                'jumlah_saldo' => $jumlahSaldo,
                'harga_emas_per_gram' => $hargaPerGram,
                'jumlah_gram' => $jumlahGram,
                'status' => 'completed',
                'catatan' => $request->catatan,
            ]);
        });

        return redirect()->route('nasabah.gold-exchange.index')
            ->with('success', 'Penukaran saldo ke emas berhasil!');
    }
}
