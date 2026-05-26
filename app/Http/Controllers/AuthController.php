<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

/**
 * Controller untuk menangani Autentikasi Pengguna (Login, Register, Logout).
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman formulir login.
     * Mengalihkan ke dashboard jika pengguna sudah terautentikasi.
     *
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function showLogin()
    {
        // Jika sudah login, langsung alihkan ke dashboard
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        // Render halaman Login menggunakan Inertia
        return Inertia::render('Auth/Login');
    }

    /**
     * Memproses percobaan login pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input email dan password menggunakan Validator Facade
        $credentials = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ])->validate();

        // Melakukan proses autentikasi (attempt login)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerasi session ID demi keamanan (mencegah Session Fixation)
            $request->session()->regenerate();
            
            $user = Auth::user();
            // Memastikan status akun dalam kondisi aktif
            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif atau ditangguhkan.',
                ]);
            }

            return redirect()->intended('/dashboard')->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        // Mengembalikan pesan error jika kredensial tidak cocok
        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ]);
    }

    /**
     * Menampilkan halaman registrasi mahasiswa baru.
     * Mengalihkan ke dashboard jika pengguna sudah terautentikasi.
     *
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function showRegister()
    {
        // Jika sudah login, alihkan ke dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        // Render halaman Register menggunakan Inertia
        return Inertia::render('Auth/Register');
    }

    /**
     * Memproses pendaftaran (registrasi) pengguna baru.
     * Secara otomatis menetapkan role 'mahasiswa' dan melakukan login otomatis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validasi data input registrasi menggunakan Validator Facade
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'npm' => 'required|string|unique:users',
            'telegram_chat_id' => 'required|string',
            'kategori' => 'required|in:magang,skripsi',
        ], [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah terdaftar.',
            'min' => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi password tidak cocok.',
        ], [
            'name' => 'Nama Lengkap',
            'email' => 'Alamat Email',
            'password' => 'Password',
            'npm' => 'NPM',
            'telegram_chat_id' => 'Telegram Chat ID',
            'kategori' => 'Kategori',
        ])->validate();

        // Menyimpan data pengguna baru menggunakan query builder
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'npm' => $request->npm,
            'telegram_chat_id' => $request->telegram_chat_id,
            'kategori' => $request->kategori,
            'status' => 'active',
        ]);

        // Mendapatkan objek role 'mahasiswa'
        $role = Role::query()->where('name', 'mahasiswa')->first();
        if ($role) {
            // Sinkronisasi role
            $user->role_id = $role->id;
        }

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Registrasi berhasil! Selamat datang di TAMP.');
    }

    /**
     * Memproses logout pengguna, membatalkan sesi saat ini.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Melakukan proses logout
        Auth::logout();
        // Membatalkan sesi pengguna saat ini
        $request->session()->invalidate();
        // Regenerasi CSRF token baru demi keamanan
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
