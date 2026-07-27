<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Exceptions\SearchUnavailableException;
use App\Models\Nasabah;
use App\Models\User;
use App\Services\Search\NasabahSearchRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class NasabahController extends Controller
{
    public function __construct(
        private NasabahSearchRepository $nasabahSearch
    ) {}

    public function index(Request $request)
    {
        if ($request->filled('nasabah')) {
            try {
                $result = $this->nasabahSearch->search($request->nasabah, 10, $request->get('page', 1));
                $nasabah = $result['result'];
            } catch (SearchUnavailableException $e) {
                $result = $this->nasabahSearch->mysqlFallback($request->nasabah, 10, $request->get('page', 1));
                $nasabah = $result['result'];
            }
        } else {
            $nasabah = Nasabah::latest()->paginate(10)->withQueryString();
        }

        return view('admin.nasabah.index', compact('nasabah'));
    }

    public function create()
    {
        return view('admin.nasabah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'size:16'],
            'nama' => ['required', 'min:3', 'max:100'],
            'username' => ['required', 'min:5', 'max:30'],
            'password' => ['required', 'min:8'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tanggal_lahir' => ['required', 'date'],
            'tempat_lahir' => ['required', 'max:100'],
            'alamat' => ['required', 'max:255'],
            'no_hp' => ['required', 'min:10', 'max:13'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'nasabah',
            ]);

            Nasabah::create([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'nama' => $request->nama,
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

        if ($nasabah->setorans()->exists()) {
            return redirect()->route('admin.nasabah.index')->with('error', 'Nasabah tidak dapat dihapus karena masih memiliki riwayat setoran.');
        }

        $nasabah->delete();

        return redirect()->route('admin.nasabah.index')->with('success', 'Nasabah berhasil dihapus');
    }

    public function search(Request $request)
    {
        if ($request->filled('nasabah')) {
            try {
                $result = $this->nasabahSearch->search($request->nasabah, 10, $request->get('page', 1));
                $nasabah = $result['result'];
            } catch (SearchUnavailableException $e) {
                $result = $this->nasabahSearch->mysqlFallback($request->nasabah, 10, $request->get('page', 1));
                $nasabah = $result['result'];
            }
        } else {
            $nasabah = Nasabah::latest()->paginate(10)->withQueryString();
        }

        return view('admin.nasabah.index', compact('nasabah'));
    }
}
