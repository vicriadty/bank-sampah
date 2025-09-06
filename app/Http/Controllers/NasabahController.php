<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NasabahController extends Controller
{
    public function index()
    {
        $nasabah = Nasabah::all();

        return view('pages.nasabah.index', [
            'nasabah' => $nasabah,
        ]);
    }

    public function create()
    {
        return view('pages.nasabah.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nik' => ['required', 'min:16', 'max:16'],
            'nama' => ['required', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'tanggal_lahir' => ['required', 'string'],
            'tempat_lahir' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['required', 'max:13'],
            'saldo' => ['nullable', 'numeric'],
            'total_sampah' => ['nullable', 'numeric'],
        ]);

        Nasabah::create($validatedData);

        return redirect('/nasabah')->with('success', 'Data Nasabah berhasil ditambahkan');
    }

    public function edit($id)
    {
        $nasabah = Nasabah::findOrFail($id);

        return view('pages.nasabah.edit', [
            'nasabah' => $nasabah,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nik' => ['required', 'min:16', 'max:16'],
            'nama' => ['required', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'tanggal_lahir' => ['required', 'string'],
            'tempat_lahir' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['max:15'],
        ]);

        Nasabah::findOrFail($id)->update($validatedData);

        return redirect('/nasabah')->with('success', 'Nasabah berhasil diupdate');
    }

    public function destroy($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        $nasabah->delete();

        return redirect('/nasabah')->with('success', 'Nasabah berhasil dihapus');
    }

    public function search(Request $request)
    {
        // $search = $request->q;

        // $nasabahs = Nasabah::where('nama', 'LIKE', "%{$search}%")
        //     ->orderBy('nama')
        //     ->limit(20)
        //     ->get();

        // $formattedNasabahs = [];

        // foreach ($nasabahs as $nasabah) {
        //     $formattedNasabahs[] = ['id' => $nasabah->id, 'text' => $nasabah->nama];
        // }

        // return response()->json($formattedNasabahs);

        $nasabah = Nasabah::where('nama', 'LIKE', '%' . $request->nasabah . '%')->get();
        return view('pages.nasabah.index', compact('nasabah'));
    }
}
