<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pengepul;


use Illuminate\Http\Request;

class PengepulController extends Controller
{
    public function index()
    {
        $pengepul = Pengepul::all();
        return view('admin.pengepul.index', [
            'pengepul' => $pengepul,
        ]);
    }

    public function create()
    {
        return view('admin.pengepul.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['required', 'max:15'],
        ]);

        Pengepul::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.pengepul.index'),
            ]);
        }

        return redirect()->route('admin.pengepul.index')->with('success', 'Pengepul berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pengepul = Pengepul::findOrFail($id);

        return view('admin.pengepul.edit', [
            'pengepul' => $pengepul,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['required', 'max:15'],
        ]);

        $pengepul = Pengepul::findOrFail($id);
        $pengepul->update($request->all());

        return redirect()->route('admin.pengepul.index')->with('success', 'Pengepul berhasil diupdate');
    }

    public function destroy($id)
    {
        $pengepul = Pengepul::findOrFail($id);
        $pengepul->delete();

        return redirect()->route('admin.pengepul.index')->with('success', 'Pengepul berhasil dihapus');
    }

    public function search(Request $request)
    {
        $pengepul = Pengepul::where('nama', 'LIKE', '%' . $request->pengepul . '%')->get();
        return view('admin.pengepul.index', compact('pengepul'));
    }
}
