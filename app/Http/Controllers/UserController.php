<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Only super_admin and ka_prodi can manage users
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $roleName = $currentUser->roles->first()?->name;
        if (!in_array($roleName, ['super_admin', 'ka_prodi'])) {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $search = $request->input('search');
        
        $users = User::with(['roles', 'dosenPembimbing'])
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('npm', 'like', "%{$search}%")
                    ->orWhere('nidn', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::all();
        // Lecturers are users who have role dosen, ka_prodi or have a nidn
        $dosens = User::whereNotNull('nidn')
            ->orWhereHas('roles', function($q) {
                $q->whereIn('name', ['dosen', 'ka_prodi']);
            })->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'dosens' => $dosens,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
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
        ]);

        $user = User::create([
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

        $user->role_id = $request->role_id;

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
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
        ]);

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

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $user->update($userData);
        $user->role_id = $request->role_id;

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}
