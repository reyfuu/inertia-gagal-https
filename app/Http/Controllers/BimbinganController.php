<?php

namespace App\Http\Controllers;

use App\Models\Bimbingan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

/**
 * Controller untuk mengelola data Bimbingan Tugas Akhir (Skripsi/Proposal).
 * Mengatur alur pengajuan bimbingan oleh mahasiswa, peninjauan oleh dosen, dan manajemen penuh oleh admin.
 */
class BimbinganController extends Controller
{
    /**
     * Menampilkan daftar bimbingan sesuai filter pencarian dan peran pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $search = $request->input('search');

        // Query utama data bimbingan dengan eager loading relasi mahasiswa (user) dan dosen menggunakan query builder
        $query = Bimbingan::query()->with(['user:id,name', 'dosen:id,name'])
            ->when($search, function($q, $search) {
                $q->where('topik', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });

        // Filter data berdasarkan peran (role) pengguna
        if ($roleName === 'dosen') {
            // Dosen hanya dapat melihat bimbingan yang ditujukan kepada dirinya
            $query->where('dosen_id', $user->id);
        } elseif ($roleName === 'mahasiswa') {
            // Mahasiswa hanya dapat melihat bimbingannya sendiri
            $query->where('user_id', $user->id);
        } // Admin/Kaprodi tidak di-filter (dapat melihat semua bimbingan)

        // Mengambil data bimbingan berpaginasi
        $bimbingans = $query->latest()->paginate(10)->withQueryString();

        // Inisialisasi daftar mahasiswa dan dosen untuk dropdown form
        $mahasiswas = [];
        $dosens = [];

        // Hanya Admin dan Kaprodi yang membutuhkan daftar dropdown lengkap mahasiswa & dosen
        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $mahasiswas = User::query()->whereHas('roles', function($q) {
                $q->where('name', 'mahasiswa');
            })->get();
            $dosens = User::query()->whereNotNull('nidn')->orWhereHas('roles', function($q) {
                $q->whereIn('name', ['dosen', 'ka_prodi']);
            })->get();
        }

        // Merender view halaman Bimbingan menggunakan Inertia
        return Inertia::render('Bimbingan/Index', [
            'bimbingans' => $bimbingans,
            'mahasiswas' => $mahasiswas,
            'dosens' => $dosens,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menyimpan data bimbingan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        // Aturan validasi dasar
        $rules = [
            'topik' => 'required|string|min:5|max:255',
            'tanggal' => 'required|date',
            'type' => 'required|in:skripsi,proposal',
            'isi' => 'required|string',
        ];

        // Jika pembuat request adalah Admin/Kaprodi, diperlukan parameter tambahan
        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['user_id'] = 'required|exists:users,id';
            $rules['dosen_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:Review,Disetujui,Ditolak';
        }

        // Validasi input data menggunakan Validator Facade
        Validator::make($request->all(), $rules, [
            'required' => ':attribute wajib diisi.',
            'min' => ':attribute minimal :min karakter.',
            'date' => 'Format tanggal tidak valid.',
        ], [
            'topik' => 'Topik Pertemuan',
            'tanggal' => 'Tanggal',
            'type' => 'Jenis',
            'isi' => 'Isi Bimbingan',
            'user_id' => 'Mahasiswa',
            'dosen_id' => 'Dosen',
            'status' => 'Status',
        ])->validate();

        $bimbinganData = [
            'topik' => $request->topik,
            'tanggal' => $request->tanggal,
            'type' => $request->type,
            'isi' => $request->isi,
        ];

        // Pengisian field berdasarkan role
        if ($roleName === 'mahasiswa') {
            // Mahasiswa otomatis mereferensikan dirinya dan dosen pembimbingnya
            $bimbinganData['user_id'] = $user->id;
            $bimbinganData['dosen_id'] = $user->dosen_pembimbing_id;
            $bimbinganData['status'] = 'Review';
            // Menghitung jumlah revisi/pertemuan bimbingan mahasiswa tersebut
            $bimbinganData['revision_count'] = Bimbingan::query()->where('user_id', $user->id)->count() + 1;
        } else {
            // Admin/Kaprodi mengisi manual mahasiswa dan dosen tujuan
            $bimbinganData['user_id'] = $request->user_id;
            $bimbinganData['dosen_id'] = $request->dosen_id;
            $bimbinganData['status'] = $request->status ?? 'Review';
            $bimbinganData['komentar'] = $request->komentar;
            $bimbinganData['revision_count'] = Bimbingan::query()->where('user_id', $request->user_id)->count() + 1;
        }

        // Membuat bimbingan baru menggunakan query builder
        Bimbingan::query()->create($bimbinganData);

        return redirect()->route('bimbingan.index')->with('success', 'Bimbingan berhasil ditambahkan.');
    }

    /**
     * Memperbarui data bimbingan di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bimbingan  $bimbingan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Bimbingan $bimbingan)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $rules = [
            'topik' => 'required|string|min:5|max:255',
            'tanggal' => 'required|date',
            'type' => 'required|in:skripsi,proposal',
            'isi' => 'required|string',
        ];

        // Dosen hanya boleh mengupdate status dan menambahkan komentar saja
        if ($roleName === 'dosen') {
            $rules = [
                'status' => 'required|in:Review,Disetujui,Ditolak',
                'komentar' => 'nullable|string',
            ];
        } elseif (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            // Admin dapat merubah semua atribut bimbingan
            $rules['user_id'] = 'required|exists:users,id';
            $rules['dosen_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:Review,Disetujui,Ditolak';
            $rules['komentar'] = 'nullable|string';
        }

        // Validasi input menggunakan Validator Facade
        Validator::make($request->all(), $rules, [
            'required' => ':attribute wajib diisi.',
            'min' => ':attribute minimal :min karakter.',
        ], [
            'topik' => 'Topik Pertemuan',
            'tanggal' => 'Tanggal',
            'type' => 'Jenis',
            'isi' => 'Isi Bimbingan',
            'user_id' => 'Mahasiswa',
            'dosen_id' => 'Dosen',
            'status' => 'Status',
            'komentar' => 'Komentar Dosen',
        ])->validate();

        if ($roleName === 'dosen') {
            // Proses update untuk dosen (hanya status dan komentar)
            Bimbingan::query()->where('id', $bimbingan->id)->update([
                'status' => $request->status,
                'komentar' => $request->komentar,
            ]);
        } elseif ($roleName === 'mahasiswa') {
            // Mahasiswa hanya bisa mengubah bimbingan miliknya sendiri
            if ($bimbingan->user_id !== $user->id) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengubah bimbingan ini.');
            }
            // Mengubah data bimbingan mahasiswa (status di-reset ke Review)
            Bimbingan::query()->where('id', $bimbingan->id)->update([
                'topik' => $request->topik,
                'tanggal' => $request->tanggal,
                'type' => $request->type,
                'isi' => $request->isi,
                'status' => 'Review',
            ]);
        } else {
            // Admin melakukan update penuh
            Bimbingan::query()->where('id', $bimbingan->id)->update([
                'user_id' => $request->user_id,
                'dosen_id' => $request->dosen_id,
                'topik' => $request->topik,
                'tanggal' => $request->tanggal,
                'type' => $request->type,
                'status' => $request->status,
                'isi' => $request->isi,
                'komentar' => $request->komentar,
            ]);
        }

        return redirect()->route('bimbingan.index')->with('success', 'Bimbingan berhasil diperbarui.');
    }

    /**
     * Mengambil riwayat komentar dosen dari bimbingan-bimbingan sebelumnya milik mahasiswa tertentu.
     * Mengembalikan data dalam bentuk JSON (digunakan untuk modal AJAX).
     *
     * @param  \App\Models\Bimbingan  $bimbingan
     * @return \Illuminate\Http\JsonResponse
     */
    public function komentarHistory(Bimbingan $bimbingan)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        // Eager load data mahasiswa pembuat bimbingan
        $bimbingan->load('user:id,name');

        // Mencegah dosen lain melihat riwayat komentar bimbingan ini
        if ($roleName === 'dosen' && $bimbingan->dosen_id !== $user->id) {
            abort(403);
        }

        // Mencegah mahasiswa lain melihat riwayat komentar bimbingan ini
        if ($roleName === 'mahasiswa' && $bimbingan->user_id !== $user->id) {
            abort(403);
        }

        // Mengambil semua riwayat komentar bimbingan mahasiswa terkait yang pernah diisi komentarnya
        $comments = Bimbingan::query()
            ->with('dosen:id,name')
            ->where('user_id', $bimbingan->user_id)
            ->whereNotNull('komentar')
            ->where('komentar', '!=', '')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get(['id', 'topik', 'komentar', 'tanggal', 'dosen_id'])
            ->map(fn (Bimbingan $item) => [
                'id' => $item->id,
                'topik' => $item->topik,
                'komentar' => $item->komentar,
                'tanggal' => $item->tanggal,
                'dosen_name' => $item->dosen?->name ?? '-',
            ]);

        // Mengembalikan respon berformat JSON
        return response()->json([
            'mahasiswa_name' => $bimbingan->user?->name,
            'comments' => $comments,
        ]);
    }

    /**
     * Menghapus data bimbingan dari database.
     *
     * @param  \App\Models\Bimbingan  $bimbingan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Bimbingan $bimbingan)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        // Mahasiswa dilarang menghapus bimbingan milik orang lain
        if ($roleName === 'mahasiswa' && $bimbingan->user_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus bimbingan ini.');
        }

        // Menghapus bimbingan menggunakan query builder
        Bimbingan::query()->where('id', $bimbingan->id)->delete();

        return redirect()->route('bimbingan.index')->with('success', 'Bimbingan berhasil dihapus.');
    }
}
