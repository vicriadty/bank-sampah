<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Nasabah;

class ProfileController extends Controller
{
    public function index()
    {
        $nasabah = Auth::user()->nasabah;
        return view('nasabah.profile', compact('nasabah'));
    }

    public function update(Request $request)
    {
        $nasabah = Auth::user()->nasabah;
        
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        $nasabah->update($request->only('nama', 'no_hp', 'alamat'));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
