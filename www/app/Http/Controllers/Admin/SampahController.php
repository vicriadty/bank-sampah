<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\KategoriSampah;
use App\Models\JenisSampah;
use Illuminate\Http\Request;

class SampahController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisSampah::with('kategoriSampah');

        if ($request->filled('nama_kategori')) {
            $query->whereHas('kategoriSampah', function ($q) use ($request) {
                $q->where('nama_kategori', $request->nama_kategori);
            });
        }

        $sampah = $query->latest()->paginate(10)->withQueryString();
        $kategoriSampahs = KategoriSampah::all();

        return view('admin.sampah.index', compact('sampah', 'kategoriSampahs'));
    }

    public function create()
    {
        $kategoriSampahs = KategoriSampah::all();
        return view('admin.sampah.create', compact('kategoriSampahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => ['required', 'exists:kategori_sampahs,id'],
            'nama_jenis' => ['required', 'max:100'],
            'harga_per_kg' => ['required', 'numeric'],
        ]);

        JenisSampah::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.sampah.index'),
            ]);
        }

        return redirect()->route('admin.sampah.index')->with('success', 'Data sampah berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $sampah = JenisSampah::findOrFail($id);
        $kategoriSampahs = KategoriSampah::all();

        return view('admin.sampah.edit', compact('sampah', 'kategoriSampahs'));
    }

    public function getForEdit($id)
    {
        $sampah = JenisSampah::findOrFail($id);

        return response()->json($sampah);
    }

    public function update(Request $request, $id)
    {
        $sampah = JenisSampah::findOrFail($id);

        $validatedData = $request->validate([
            'kategori_id' => ['required', 'exists:kategori_sampahs,id'],
            'nama_jenis' => ['required', 'max:100'],
            'harga_per_kg' => ['required', 'numeric'],
        ]);

        $sampah->update($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.sampah.index'),
            ]);
        }

        return redirect()->route('admin.sampah.index')->with('success', 'Sampah berhasil di update');
    }

    public function destroy($id)
    {
        $sampah = JenisSampah::findOrFail($id);

        if ($sampah->stok > 0) {
            return redirect()->route('admin.sampah.index')->with('error', 'Sampah tidak dapat dihapus karena masih memiliki stok.');
        }

        $sampah->delete();

        return redirect()->route('admin.sampah.index')->with('success', 'Sampah berhasil dihapus');
    }
}
