<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\LaporanMingguan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk mengelola Laporan Mingguan.
 * Memfasilitasi pencatatan kemajuan mingguan mahasiswa, persetujuan oleh dosen pembimbing, dan administrasi laporan mingguan.
 */
class LaporanMingguanController extends Controller
{
    /**
     * Menampilkan daftar laporan mingguan sesuai filter pencarian, filter dropdown mahasiswa, dan peran pengguna.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        // Mahasiswa skripsi tidak boleh mengakses halaman laporan mingguan
        if ($roleName === 'mahasiswa' && $user->kategori !== 'magang') {
            return redirect('/dashboard')->with('error', 'Halaman Laporan Mingguan hanya tersedia untuk mahasiswa Magang.');
        }

        $search = $request->input('search');
        $filterMahasiswa = $request->input('mahasiswa_id');

        // Query utama laporan mingguan beserta relasi mahasiswa (user) dan dosen pembimbing menggunakan query builder
        $query = LaporanMingguan::query()->with(['mahasiswa', 'dosen']);

        // Filter pencarian berdasarkan nama mahasiswa atau nomor minggu
        if ($search) {
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('week', 'like', "%{$search}%");
        }

        // Filter dropdown untuk menampilkan laporan mingguan dari mahasiswa tertentu
        if ($filterMahasiswa) {
            $query->where('mahasiswa_id', $filterMahasiswa);
        }

        // Filter data berdasarkan peran (role) pengguna
        if ($roleName === 'dosen') {
            // Dosen hanya dapat melihat laporan mingguan mahasiswa bimbingannya
            $query->where('dosen_id', $user->id);
        } elseif ($roleName === 'mahasiswa') {
            // Mahasiswa hanya dapat melihat laporan mingguan miliknya sendiri
            $query->where('mahasiswa_id', $user->id);
        }

        // Mengambil data dengan paginasi
        $laporanMingguans = $query->latest()->paginate(10)->withQueryString();

        // Mendapatkan opsi daftar mahasiswa bimbingan yang memiliki laporan mingguan untuk dropdown filter
        $filterMahasiswas = User::query()->whereHas('laporanMingguans', function ($q) use ($roleName, $user) {
            if ($roleName === 'dosen') {
                $q->where('dosen_id', $user->id);
            }
        })->get();

        // Menampilkan opsi semua mahasiswa jika diakses oleh Admin/Kaprodi untuk formulir pembuatan
        // Filter mahasiswas: hanya mahasiswa dengan kategori magang yang dapat memiliki laporan mingguan
        $mahasiswas = [];
        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $mahasiswas = User::query()->whereHas('roles', function ($q) {
                $q->where('name', 'mahasiswa');
            })->where('kategori', 'magang')->get();
        }

        // Merender view Laporan Mingguan menggunakan Inertia
        return Inertia::render('LaporanMingguan/Index', [
            'laporanMingguans' => $laporanMingguans,
            'filterMahasiswas' => $filterMahasiswas,
            'mahasiswas' => $mahasiswas,
            'filters' => $request->only(['search', 'mahasiswa_id']),
        ]);
    }

    /**
     * Menyimpan data laporan mingguan baru ke database.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        // Mahasiswa skripsi tidak boleh membuat laporan mingguan
        if ($roleName === 'mahasiswa' && $user->kategori !== 'magang') {
            return back()->with('error', 'Fitur Laporan Mingguan hanya tersedia untuk mahasiswa Magang.');
        }

        // Aturan validasi dasar
        $rules = [
            'week' => 'required|integer|min:1',
            'isi' => 'required|string',
        ];

        // Jika pembuat request adalah Admin/Kaprodi, diperlukan parameter tambahan
        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['mahasiswa_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,disetujui,ditolak';
        }

        // Validasi input data menggunakan Validator Facade
        Validator::make($request->all(), $rules, [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'min' => ':attribute minimal bernilai :min.',
        ], [
            'week' => 'Minggu Ke',
            'isi' => 'Isi / Link Bimbingan',
            'mahasiswa_id' => 'Mahasiswa',
            'status' => 'Status',
        ])->validate();

        $data = [
            'week' => $request->week,
            'isi' => $request->isi,
        ];

        // Pengisian field berdasarkan role pembuat request
        if ($roleName === 'mahasiswa') {
            // Mahasiswa wajib memiliki Laporan Akademik terlebih dahulu
            $laporan = Laporan::query()->where('mahasiswa_id', $user->id)->latest()->first();
            if (! $laporan) {
                return back()->with('error', 'Anda harus memiliki Laporan Akademik terlebih dahulu sebelum menambahkan Laporan Mingguan.');
            }
            $data['mahasiswa_id'] = $user->id;
            $data['laporan_id'] = $laporan->id;
            $data['dosen_id'] = $laporan->dosen_id ?: $user->dosen_pembimbing_id;
            $data['status'] = 'pending';
        } else {
            // Admin/Kaprodi mengisi manual mahasiswa tujuan
            $student = User::query()->find($request->mahasiswa_id);
            // Mahasiswa yang dipilih wajib memiliki Laporan Akademik
            $laporan = Laporan::query()->where('mahasiswa_id', $request->mahasiswa_id)->latest()->first();
            if (! $laporan) {
                return back()->with('error', 'Mahasiswa yang dipilih tidak memiliki Laporan Akademik.');
            }
            $data['mahasiswa_id'] = $request->mahasiswa_id;
            $data['laporan_id'] = $laporan->id;
            $data['dosen_id'] = $laporan->dosen_id ?: $student->dosen_pembimbing_id;
            $data['status'] = $request->status ?? 'pending';
        }

        // Membuat laporan mingguan baru menggunakan query builder
        LaporanMingguan::query()->create($data);

        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan berhasil ditambahkan.');
    }

    /**
     * Memperbarui data laporan mingguan di database.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, LaporanMingguan $laporanMingguan)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        // Mahasiswa skripsi tidak boleh mengubah laporan mingguan
        if ($roleName === 'mahasiswa' && $user->kategori !== 'magang') {
            return back()->with('error', 'Fitur Laporan Mingguan hanya tersedia untuk mahasiswa Magang.');
        }

        $rules = [
            'week' => 'required|integer|min:1',
            'isi' => 'required|string',
        ];

        // Dosen hanya boleh memperbarui status persetujuan laporan mingguan
        if ($roleName === 'dosen') {
            $rules = [
                'status' => 'required|in:pending,disetujui,ditolak',
            ];
        } elseif (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            // Admin dapat merubah semua atribut laporan mingguan
            $rules['mahasiswa_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,disetujui,ditolak';
        }

        // Validasi input menggunakan Validator Facade
        Validator::make($request->all(), $rules, [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'min' => ':attribute minimal bernilai :min.',
        ], [
            'week' => 'Minggu Ke',
            'isi' => 'Isi / Link Bimbingan',
            'mahasiswa_id' => 'Mahasiswa',
            'status' => 'Status',
        ])->validate();

        if ($roleName === 'dosen') {
            // Proses update untuk dosen (hanya status persetujuan)
            LaporanMingguan::query()->where('id', $laporanMingguan->id)->update([
                'status' => $request->status,
            ]);
        } elseif ($roleName === 'mahasiswa') {
            // Mahasiswa hanya bisa mengubah laporan mingguan miliknya sendiri
            if ($laporanMingguan->mahasiswa_id !== $user->id) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengubah laporan ini.');
            }
            // Mengubah data laporan mingguan (status di-reset ke pending)
            LaporanMingguan::query()->where('id', $laporanMingguan->id)->update([
                'week' => $request->week,
                'isi' => $request->isi,
                'status' => 'pending',
            ]);
        } else {
            // Admin melakukan update penuh
            $student = User::query()->find($request->mahasiswa_id);
            $laporan = Laporan::query()->where('mahasiswa_id', $request->mahasiswa_id)->latest()->first();
            if (! $laporan) {
                return back()->with('error', 'Mahasiswa yang dipilih tidak memiliki Laporan Akademik.');
            }
            LaporanMingguan::query()->where('id', $laporanMingguan->id)->update([
                'mahasiswa_id' => $request->mahasiswa_id,
                'laporan_id' => $laporan->id,
                'dosen_id' => $laporan->dosen_id ?: $student->dosen_pembimbing_id,
                'week' => $request->week,
                'isi' => $request->isi,
                'status' => $request->status,
            ]);
        }

        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan berhasil diperbarui.');
    }

    /**
     * Menghapus data laporan mingguan dari database.
     *
     * @return RedirectResponse
     */
    public function destroy(LaporanMingguan $laporanMingguan)
    {
        // Mendapatkan user yang sedang login saat ini
        /** @var User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        // Mahasiswa skripsi tidak boleh menghapus laporan mingguan
        if ($roleName === 'mahasiswa' && $user->kategori !== 'magang') {
            return back()->with('error', 'Fitur Laporan Mingguan hanya tersedia untuk mahasiswa Magang.');
        }

        // Mahasiswa dilarang menghapus laporan mingguan milik orang lain
        if ($roleName === 'mahasiswa' && $laporanMingguan->mahasiswa_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus laporan ini.');
        }

        // Menghapus laporan mingguan menggunakan query builder
        LaporanMingguan::query()->where('id', $laporanMingguan->id)->delete();

        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan berhasil dihapus.');
    }
}
