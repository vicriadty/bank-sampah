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
    public function index(Request $request)
    {
        $query = Nasabah::query();

        if ($request->filled('nasabah')) {
            $query->where('nama', 'LIKE', '%' . $request->nasabah . '%');
        }

        $nasabah = $query->latest()->paginate(10)->withQueryString();

        return view('admin.nasabah.index', compact('nasabah'));
    }

    public function create()
    {
        return view('admin.nasabah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'min:16', 'max:16'],
            'nama' => ['required', 'max:100'],
            'username' => ['required', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tanggal_lahir' => ['required', 'string'],
            'tempat_lahir' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['required', 'max:13'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'nasabah',
            ]);

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

        return view('admin.nasabah.edit', compact('nasabah'));
    }

    public function getForEdit($id)
    {
        $nasabah = Nasabah::findOrFail($id);

        return response()->json($nasabah);
    }

    public function update(Request $request, $id)
    {
        $nasabah = Nasabah::findOrFail($id);

        $validatedData = $request->validate([
            'nik' => ['required', 'min:16', 'max:16'],
            'nama' => ['required', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tanggal_lahir' => ['required', 'string'],
            'tempat_lahir' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['required', 'max:15'],
        ]);

        $nasabah->update($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.nasabah.index'),
            ]);
        }

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
        $query = Nasabah::query();

        if ($request->filled('nasabah')) {
            $query->where('nama', 'LIKE', '%' . $request->nasabah . '%');
        }

        $nasabah = $query->latest()->paginate(10)->withQueryString();

        return view('admin.nasabah.index', compact('nasabah'));
    }
}
