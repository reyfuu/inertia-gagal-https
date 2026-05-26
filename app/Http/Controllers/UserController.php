<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

/**
 * Controller untuk mengelola data Pengguna (User).
 * Menyediakan fungsi CRUD yang hanya dapat diakses oleh administrator (super_admin & ka_prodi).
 */
class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna dengan fitur pencarian dan paginasi.
     * Hanya dapat diakses oleh pengguna dengan role super_admin atau ka_prodi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $roleName = $currentUser->roles->first()?->name;

        // Membatasi akses hanya untuk super_admin dan ka_prodi
        if (!in_array($roleName, ['super_admin', 'ka_prodi'])) {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Mengambil input pencarian
        $search = $request->input('search');
        
        // Query data user beserta relasi roles dan dosen pembimbingnya menggunakan query builder
        $users = User::query()->with(['roles', 'dosenPembimbing'])
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('npm', 'like', "%{$search}%")
                    ->orWhere('nidn', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Mengambil semua opsi role untuk form pembuatan/pembaruan user
        $roles = Role::query()->get();

        // Dosen adalah pengguna yang memiliki NIDN atau memiliki role 'dosen' / 'ka_prodi'
        $dosens = User::query()->whereNotNull('nidn')
            ->orWhereHas('roles', function($q) {
                $q->whereIn('name', ['dosen', 'ka_prodi']);
            })->get();

        // Merender halaman index menggunakan Inertia
        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'dosens' => $dosens,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menyimpan data pengguna baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input data pengguna baru menggunakan Validator Facade
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
            'npm' => 'nullable|string|unique:users,npm',
            'nidn' => 'nullable|string|unique:users,nidn',
            'angkatan' => 'nullable|string',
            'kategori' => 'nullable|in:magang,skripsi',
            'dosen_pembimbing_id' => 'nullable|exists:users,id',
            'telegram_chat_id' => 'nullable|string',
        ], [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah terdaftar.',
            'min' => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi password tidak cocok.',
        ], [
            'name' => 'Nama Lengkap',
            'email' => 'Email',
            'password' => 'Password',
            'role_id' => 'Role',
            'status' => 'Status',
            'npm' => 'NPM',
            'nidn' => 'NIDN',
            'angkatan' => 'Angkatan',
            'kategori' => 'Kategori',
            'dosen_pembimbing_id' => 'Dosen Pembimbing',
            'telegram_chat_id' => 'Telegram Chat ID',
        ])->validate();

        // Menyimpan data utama pengguna ke dalam database menggunakan query builder
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'npm' => $request->npm,
            'nidn' => $request->nidn,
            'angkatan' => $request->angkatan,
            'kategori' => $request->kategori,
            'dosen_pembimbing_id' => $request->dosen_pembimbing_id,
            'telegram_chat_id' => $request->telegram_chat_id,
            'status' => $request->status,
        ]);

        // Mensinkronisasikan role pengguna baru via attribute setter
        $user->role_id = $request->role_id;

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Memperbarui data pengguna yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Validasi input data pengguna (abaikan keunikan untuk email/npm/nidn milik user saat ini) menggunakan Validator Facade
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
            'npm' => 'nullable|string|unique:users,npm,' . $user->id,
            'nidn' => 'nullable|string|unique:users,nidn,' . $user->id,
            'angkatan' => 'nullable|string',
            'kategori' => 'nullable|in:magang,skripsi',
            'dosen_pembimbing_id' => 'nullable|exists:users,id',
            'telegram_chat_id' => 'nullable|string',
        ], [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah terdaftar.',
            'min' => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi password tidak cocok.',
        ], [
            'name' => 'Nama Lengkap',
            'email' => 'Email',
            'password' => 'Password',
            'role_id' => 'Role',
            'status' => 'Status',
            'npm' => 'NPM',
            'nidn' => 'NIDN',
            'angkatan' => 'Angkatan',
            'kategori' => 'Kategori',
            'dosen_pembimbing_id' => 'Dosen Pembimbing',
            'telegram_chat_id' => 'Telegram Chat ID',
        ])->validate();

        // Menyusun data pengguna yang akan diperbarui
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'npm' => $request->npm,
            'nidn' => $request->nidn,
            'angkatan' => $request->angkatan,
            'kategori' => $request->kategori,
            'dosen_pembimbing_id' => $request->dosen_pembimbing_id,
            'telegram_chat_id' => $request->telegram_chat_id,
            'status' => $request->status,
        ];

        // Jika password diisi, enkripsi dan perbarui password
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        // Melakukan update data pengguna menggunakan query builder
        User::query()->where('id', $user->id)->update($userData);
        
        // Memperbarui role pengguna
        $user->role_id = $request->role_id;

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus data pengguna dari database.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Menghapus data user menggunakan query builder
        User::query()->where('id', $user->id)->delete();
        
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}
