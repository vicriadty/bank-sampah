<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldExchange;
use App\Models\Nasabah;
use Illuminate\Http\Request;

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

}
