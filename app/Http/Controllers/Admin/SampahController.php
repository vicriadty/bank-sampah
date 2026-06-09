<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\JenisSampah;
use App\Models\Sampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SampahController extends Controller
{
    public function index(Request $request)
    {
        $query = Sampah::with('jenisSampah');

        if ($request->filled('nama_jenis')) {
            $query->whereHas('jenisSampah', function ($q) use ($request) {
                $q->where('nama_jenis', $request->nama_jenis);
            });
        }

        $sampah = $query->get();
        $jenis_sampahs = JenisSampah::all();

        return view('admin.sampah.index', compact('sampah', 'jenis_sampahs'));
    }

    public function create()
    {
        $jenisSampahs = JenisSampah::all();
        return view('admin.sampah.create', compact('jenisSampahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_sampah_id' => ['required', 'exists:jenis_sampahs,id'],
            'nama_sampah' => ['required', 'max:100'],
            'harga_per_kg' => ['required', 'numeric'],
        ]);

        Sampah::create($request->all());

        return redirect()->route('admin.sampah.index')->with('success', 'Data sampah berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $sampah = Sampah::findOrFail($id);
        $jenisSampahs = JenisSampah::all();

        return view('admin.sampah.edit', [
            'sampah' => $sampah,
            'jenisSampahs' => $jenisSampahs,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'jenis_sampah_id' => ['required', 'exists:jenis_sampahs,id'],
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

    public function quickCreateJenis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_jenis' => 'required|string|max:100|unique:jenis_sampahs,nama_jenis',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('nama_jenis'),
            ], 422);
        }

        $jenis = JenisSampah::create(['nama_jenis' => $request->nama_jenis]);

        return response()->json([
            'success' => true,
            'id' => $jenis->id,
            'nama_jenis' => $jenis->nama_jenis,
        ]);
    }
}
