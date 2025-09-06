<?php

namespace App\Http\Controllers;

use App\Models\Pengepul;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengepulController extends Controller
{
    public function index()
    {
        $pengepul = Pengepul::all();
        return view('pages.pengepul.index', [
            'pengepul' => $pengepul,
        ]);
    }

    public function create()
    {
        return view('pages.pengepul.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);

        Pengepul::create($request->all());

        return redirect('/pengepul')->with('success', 'Pengepul berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pengepul = Pengepul::findOrFail($id);

        return view('pages.pengepul.edit', [
            'pengepul' => $pengepul,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);

        $pengepul = Pengepul::findOrFail($id);
        $pengepul->update($request->all());

        return redirect('/pengepul')->with('success', 'Pengepul berhasil diupdate');
    }

    public function destroy($id)
    {
        $pengepul = Pengepul::findOrFail($id);
        $pengepul->delete();

        return redirect('/pengepul')->with('success', 'Pengepul berhasil dihapus');
    }

    public function search(Request $request)
    {
        $pengepul = Pengepul::where('nama', 'LIKE', '%' . $request->pengepul . '%')->get();
        return view('pages.pengepul.index', compact('pengepul'));
    }
}
