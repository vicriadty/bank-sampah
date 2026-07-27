<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\Nasabah;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return back();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return back();
        }

        $credentials = $request->validate([
            'username' => ['required', 'string', 'min:5', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // LOGIKA REDIRECT BERDASARKAN ROLE
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/nasabah/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return back();
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            return back();
        }

        $request->validate([
            'nik'              => 'required|string|size:16|unique:nasabahs,nik',
            'nama'             => 'required|string|min:3|max:100',
            'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir'     => 'required|string|max:100',
            'tanggal_lahir'    => 'required|date|before:today',
            'alamat'           => 'required|string|max:1000',
            'no_hp'            => 'required|string|min:10|max:13',
            'username'         => 'required|string|min:5|max:30|unique:users,username',
            'password'         => 'required|min:8|confirmed',
        ], [
            'nik.required'           => 'NIK wajib diisi.',
            'nik.size'               => 'NIK harus 16 digit.',
            'nik.unique'             => 'NIK sudah terdaftar.',
            'nama.required'          => 'Nama lengkap wajib diisi.',
            'nama.min'               => 'Nama minimal 3 karakter.',
            'nama.max'               => 'Nama maksimal 100 karakter.',
            'jenis_kelamin.required' => 'Pilih jenis kelamin.',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid.',
            'tempat_lahir.required'  => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'   => 'Tanggal lahir harus sebelum hari ini.',
            'alamat.required'        => 'Alamat wajib diisi.',
            'no_hp.required'         => 'No. handphone wajib diisi.',
            'no_hp.min'              => 'No. handphone minimal 10 digit.',
            'no_hp.max'              => 'No. handphone maksimal 13 digit.',
            'username.required'      => 'Username wajib diisi.',
            'username.min'           => 'Username minimal 5 karakter.',
            'username.max'           => 'Username maksimal 30 karakter.',
            'username.unique'        => 'Username sudah digunakan.',
            'password.required'      => 'Password wajib diisi.',
            'password.min'           => 'Password minimal 8 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => 'nasabah',
            ]);

            Nasabah::create([
                'user_id'        => $user->id,
                'nik'            => $request->nik,
                'nama'           => $request->nama,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'tempat_lahir'   => $request->tempat_lahir,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'alamat'         => $request->alamat,
                'no_hp'          => $request->no_hp,
                'email'          => $request->email ?? null,
            ]);

            DB::commit();
            return redirect()->route('login')->with('success', 'Register berhasil! Silakan login!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registrasi gagal: ' . $e->getMessage())->withInput();
        }
    }
}
