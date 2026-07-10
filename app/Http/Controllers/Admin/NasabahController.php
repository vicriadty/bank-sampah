<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class NasabahController extends Controller
{
    public function index()
    {
        $nasabah = Nasabah::all();

        return view('admin.nasabah.index', [
            'nasabah' => $nasabah,
        ]);
    }

    public function create()
    {
        return view('admin.nasabah.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input dari form admin
        $request->validate([
            'nik' => ['required', 'min:16', 'max:16'],
            'nama' => ['required', 'max:100'],
            'username' => ['required', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'], // Validasi email untuk tabel users
            'password' => ['required', 'min:6'], // Validasi password untuk tabel users
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tanggal_lahir' => ['required', 'string'],
            'tempat_lahir' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['required', 'max:13'],
        ]);

        try {
            DB::beginTransaction(); // Gunakan Database Transaction agar jika salah satu gagal, data tidak akan tersimpan setengah-setengah.

            // 2. Buat akun di tabel users
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Enkripsi password
                'role' => 'nasabah', // Otomatis diset sebagai nasabah
            ]);

            // 3. Buat profil di tabel nasabah menggunakan user_id yang baru lahir
            // Dompet otomatis terbuat via Nasabah::created event
            Nasabah::create([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'nama' => $request->nama,
                'email' => $request->email,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'tempat_lahir' => $request->tempat_lahir,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('admin.nasabah.index'),
                ]);
            }

            return redirect()->route('admin.nasabah.index')->with('success', 'Nasabah & Akun Login berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollback();

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $nasabah = Nasabah::findOrFail($id);

        return view('admin.nasabah.edit', [
            'nasabah' => $nasabah,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nik' => ['required', 'min:16', 'max:16'],
            'nama' => ['required', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tanggal_lahir' => ['required', 'string'],
            'tempat_lahir' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['required', 'max:15'],
        ]);

        Nasabah::findOrFail($id)->update($validatedData);

        return redirect()->route('admin.nasabah.index')->with('success', 'Nasabah berhasil diupdate');
    }

    public function destroy($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        $nasabah->delete();

        return redirect()->route('admin.nasabah.index')->with('success', 'Nasabah berhasil dihapus');
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
        return view('admin.nasabah.index', compact('nasabah'));
    }
}
