<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldExchange;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoldExchangeController extends Controller
{
    public function index(Request $request)
    {
        $query = GoldExchange::with('nasabah')->latest();

        if ($request->filled('nasabah')) {
            $query->whereHas('nasabah', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nasabah . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $goldExchanges = $query->paginate(10);
        $nasabahs = Nasabah::all();

        return view('admin.transaksi.gold-exchange.index', compact('goldExchanges', 'nasabahs'));
    }

    public function approve($id)
    {
        $goldExchange = GoldExchange::findOrFail($id);

        if ($goldExchange->status !== 'pending') {
            return back()->with('error', 'Transaksi ini sudah diproses.');
        }

        $goldExchange->update([
            'status' => 'completed',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Penukaran emas disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $goldExchange = GoldExchange::findOrFail($id);

        if ($goldExchange->status !== 'pending') {
            return back()->with('error', 'Transaksi ini sudah diproses.');
        }

        DB::transaction(function () use ($goldExchange, $request) {
            // Kembalikan saldo nasabah
            $goldExchange->nasabah->increment('saldo', $goldExchange->jumlah_saldo);

            $goldExchange->update([
                'status' => 'rejected',
                'catatan' => $request->catatan ?? 'Ditolak oleh admin',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Penukaran emas ditolak dan saldo dikembalikan.');
    }
}
