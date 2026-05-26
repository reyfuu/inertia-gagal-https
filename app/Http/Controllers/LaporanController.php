<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

/**
 * Controller untuk mengelola Laporan Akademik (Proposal/Magang/Skripsi).
 * Menyediakan pengajuan link dokumen laporan akademik dan peninjauan status persetujuan oleh dosen.
 */
class LaporanController extends Controller
{
    /**
     * Menampilkan daftar laporan akademik sesuai filter pencarian dan peran pengguna.
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

        // Query utama data laporan akademik beserta relasi mahasiswa (user) dan dosen menggunakan query builder
        $query = Laporan::query()->with(['mahasiswa', 'dosen'])
            ->when($search, function($q, $search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function($qm) use ($search) {
                      $qm->where('name', 'like', "%{$search}%");
                  });
            });

        // Membatasi data laporan berdasarkan peran (role) pengguna
        if ($roleName === 'dosen') {
            // Dosen melihat laporan yang dibimbing olehnya
            $query->where('dosen_id', $user->id);
        } elseif ($roleName === 'mahasiswa') {
            // Mahasiswa melihat laporannya sendiri
            $query->where('mahasiswa_id', $user->id);
        }

        // Mengambil data dengan paginasi
        $laporans = $query->latest()->paginate(10)->withQueryString();

        // Inisialisasi daftar mahasiswa dan dosen untuk dropdown form
        $mahasiswas = [];
        $dosens = [];

        // Hanya Admin/Kaprodi yang membutuhkan daftar dropdown lengkap mahasiswa & dosen
        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $mahasiswas = User::query()->whereHas('roles', function($q) {
                $q->where('name', 'mahasiswa');
            })->get();
            $dosens = User::query()->whereNotNull('nidn')->orWhereHas('roles', function($q) {
                $q->whereIn('name', ['dosen', 'ka_prodi']);
            })->get();
        }

        // Merender view Laporan menggunakan Inertia
        return Inertia::render('Laporan/Index', [
            'laporans' => $laporans,
            'mahasiswas' => $mahasiswas,
            'dosens' => $dosens,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menyimpan data laporan akademik baru ke database.
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
            'judul' => 'required|string|min:10|max:255',
            'type' => 'required|in:proposal,magang,skripsi',
            'dokumen' => 'required|url',
            'tanggal_mulai' => 'required|date',
            'deskripsi' => 'nullable|string',
        ];

        // Jika pembuat request adalah Admin/Kaprodi, diperlukan parameter tambahan
        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['mahasiswa_id'] = 'required|exists:users,id';
            $rules['dosen_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,disetujui,revisi';
        }

        // Validasi input data menggunakan Validator Facade
        Validator::make($request->all(), $rules, [
            'required' => ':attribute wajib diisi.',
            'min' => ':attribute minimal :min karakter.',
            'url' => 'Format :attribute harus berupa URL valid.',
            'date' => 'Format tanggal tidak valid.',
        ], [
            'judul' => 'Judul Laporan',
            'type' => 'Jenis Laporan',
            'dokumen' => 'Link Dokumen',
            'tanggal_mulai' => 'Tanggal Mulai',
            'deskripsi' => 'Deskripsi',
            'mahasiswa_id' => 'Mahasiswa',
            'dosen_id' => 'Dosen Pembimbing',
            'status' => 'Status',
        ])->validate();

        $laporanData = [
            'judul' => $request->judul,
            'type' => $request->type,
            'dokumen' => $request->dokumen,
            'tanggal_mulai' => $request->tanggal_mulai,
            'deskripsi' => $request->deskripsi,
        ];

        // Pengisian field berdasarkan role
        if ($roleName === 'mahasiswa') {
            // Mahasiswa otomatis mereferensikan dirinya dan dosen pembimbingnya
            $laporanData['mahasiswa_id'] = $user->id;
            $laporanData['dosen_id'] = $user->dosen_pembimbing_id;
            $laporanData['status'] = 'pending';
        } else {
            // Admin/Kaprodi mengisi manual mahasiswa dan dosen tujuan
            $laporanData['mahasiswa_id'] = $request->mahasiswa_id;
            $laporanData['dosen_id'] = $request->dosen_id;
            $laporanData['status'] = $request->status ?? 'pending';
            $laporanData['komentar'] = $request->komentar;
        }

        // Membuat laporan baru menggunakan query builder
        Laporan::query()->create($laporanData);

        return redirect()->route('laporan.index')->with('success', 'Laporan akademik berhasil ditambahkan.');
    }

    /**
     * Memperbarui data laporan akademik di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Laporan  $laporan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Laporan $laporan)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $rules = [
            'judul' => 'required|string|min:10|max:255',
            'type' => 'required|in:proposal,magang,skripsi',
            'dokumen' => 'required|url',
            'tanggal_mulai' => 'required|date',
            'deskripsi' => 'nullable|string',
        ];

        // Dosen hanya diperbolehkan memperbarui status dan menambahkan komentar saja
        if ($roleName === 'dosen') {
            $rules = [
                'status' => 'required|in:pending,disetujui,revisi',
                'komentar' => 'nullable|string',
            ];
        } elseif (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            // Admin dapat merubah semua atribut laporan akademik
            $rules['mahasiswa_id'] = 'required|exists:users,id';
            $rules['dosen_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,disetujui,revisi';
            $rules['komentar'] = 'nullable|string';
        }

        // Validasi input menggunakan Validator Facade
        Validator::make($request->all(), $rules, [
            'required' => ':attribute wajib diisi.',
            'min' => ':attribute minimal :min karakter.',
            'url' => 'Format :attribute harus berupa URL valid.',
        ], [
            'judul' => 'Judul Laporan',
            'type' => 'Jenis Laporan',
            'dokumen' => 'Link Dokumen',
            'tanggal_mulai' => 'Tanggal Mulai',
            'deskripsi' => 'Deskripsi',
            'mahasiswa_id' => 'Mahasiswa',
            'dosen_id' => 'Dosen Pembimbing',
            'status' => 'Status',
            'komentar' => 'Komentar Dosen',
        ])->validate();

        if ($roleName === 'dosen') {
            // Proses update untuk dosen (hanya status dan komentar)
            Laporan::query()->where('id', $laporan->id)->update([
                'status' => $request->status,
                'komentar' => $request->komentar,
            ]);
        } elseif ($roleName === 'mahasiswa') {
            // Mahasiswa hanya bisa mengubah laporan miliknya sendiri
            if ($laporan->mahasiswa_id !== $user->id) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengubah laporan ini.');
            }
            // Mengubah data laporan (status di-reset ke pending)
            Laporan::query()->where('id', $laporan->id)->update([
                'judul' => $request->judul,
                'type' => $request->type,
                'dokumen' => $request->dokumen,
                'tanggal_mulai' => $request->tanggal_mulai,
                'deskripsi' => $request->deskripsi,
                'status' => 'pending',
            ]);
        } else {
            // Admin melakukan update penuh
            Laporan::query()->where('id', $laporan->id)->update([
                'mahasiswa_id' => $request->mahasiswa_id,
                'dosen_id' => $request->dosen_id,
                'judul' => $request->judul,
                'type' => $request->type,
                'dokumen' => $request->dokumen,
                'tanggal_mulai' => $request->tanggal_mulai,
                'status' => $request->status,
                'deskripsi' => $request->deskripsi,
                'komentar' => $request->komentar,
            ]);
        }

        return redirect()->route('laporan.index')->with('success', 'Laporan akademik berhasil diperbarui.');
    }

    /**
     * Menghapus data laporan akademik dari database.
     *
     * @param  \App\Models\Laporan  $laporan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Laporan $laporan)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        // Mahasiswa dilarang menghapus laporan milik orang lain
        if ($roleName === 'mahasiswa' && $laporan->mahasiswa_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus laporan ini.');
        }

        // Menghapus laporan menggunakan query builder
        Laporan::query()->where('id', $laporan->id)->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan akademik berhasil dihapus.');
    }
}
