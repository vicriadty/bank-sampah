<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\JenisSampah;
use App\Models\Sampah;
use Illuminate\Http\Request;

class SampahController extends Controller
{
    public function index()
    {
        $sampah = Sampah::all();

        return view('admin.sampah.index', [
            'sampah' => $sampah,
        ]);
    }

    // public function indexStokSampah()
    // {
    //     $stokSampah = Setoran::with('sampah, nasabah')
    //         ->select('sampah_id', DB::raw('SUM(berat) as total_berat'))
    //         ->groupBy('sampah_id')
    //         ->get();

    //     return view('admin.stok-sampah.index', [
    //         'stokSampah' => $stokSampah,
    //     ]);
    // }

    public function create()
    {
        $jenisSampahs = JenisSampah::all();
        return view('admin.sampah.create', compact('jenisSampahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_sampah_id' => 'required|exists:jenis_sampahs,id',
            'nama_sampah' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
        ]);

        Sampah::create($request->all());

        return redirect()->route('admin.sampah.index')->with('success', 'Data sampah berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $sampah = Sampah::findOrFail($id);

        return view('admin.sampah.edit', [
            'sampah' => $sampah,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'jenis_sampah_id' => ['required', 'min:16', 'max:16'],
            'nama_sampah' => ['required', 'max:100'],
            'harga_per_kg' => ['required', 'numeric'],
        ]);

        Sampah::findOrFail($id)->update($validatedData);

        return redirect()->route('admin.sampah.index')->with('success', 'Sampah berhasil di update');
    }

    public function destroy($id)
    {
        $sampah = Sampah::findOrFail($id);
        $sampah->delete();

        return redirect()->route('admin.sampah.index')->with('success', 'Sampah berhasil dihapus');
    }
}
