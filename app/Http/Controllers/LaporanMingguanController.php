<?php

namespace App\Http\Controllers;

use App\Models\LaporanMingguan;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LaporanMingguanController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $search = $request->input('search');
        $filterMahasiswa = $request->input('mahasiswa_id');

        $query = LaporanMingguan::with(['mahasiswa', 'dosen']);

        // Filter search
        if ($search) {
            $query->whereHas('mahasiswa', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('week', 'like', "%{$search}%");
        }

        // Filter Mahasiswa (dropdown filter)
        if ($filterMahasiswa) {
            $query->where('mahasiswa_id', $filterMahasiswa);
        }

        // Filter based on roles
        if ($roleName === 'dosen') {
            $query->where('dosen_id', $user->id);
        } elseif ($roleName === 'mahasiswa') {
            $query->where('mahasiswa_id', $user->id);
        }

        $laporanMingguans = $query->latest()->paginate(10)->withQueryString();

        // Get filter options (mahasiswas who have weekly reports)
        $filterMahasiswas = User::whereHas('laporanMingguans', function($q) use ($roleName, $user) {
            if ($roleName === 'dosen') {
                $q->where('dosen_id', $user->id);
            }
        })->get();

        // Admin mahasiswas selection
        $mahasiswas = [];
        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $mahasiswas = User::whereHas('roles', function($q) {
                $q->where('name', 'mahasiswa');
            })->get();
        }

        return Inertia::render('LaporanMingguan/Index', [
            'laporanMingguans' => $laporanMingguans,
            'filterMahasiswas' => $filterMahasiswas,
            'mahasiswas' => $mahasiswas,
            'filters' => $request->only(['search', 'mahasiswa_id']),
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $rules = [
            'week' => 'required|integer|min:1',
            'isi' => 'required|string',
        ];

        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['mahasiswa_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,disetujui,ditolak';
        }

        $request->validate($rules, [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'min' => ':attribute minimal bernilai :min.',
        ], [
            'week' => 'Minggu Ke',
            'isi' => 'Isi / Link Bimbingan',
            'mahasiswa_id' => 'Mahasiswa',
            'status' => 'Status',
        ]);

        $data = [
            'week' => $request->week,
            'isi' => $request->isi,
        ];

        if ($roleName === 'mahasiswa') {
            // Find active student academic report
            $laporan = Laporan::where('mahasiswa_id', $user->id)->latest()->first();
            if (!$laporan) {
                return back()->with('error', 'Anda harus memiliki Laporan Akademik terlebih dahulu sebelum menambahkan Laporan Mingguan.');
            }
            $data['mahasiswa_id'] = $user->id;
            $data['laporan_id'] = $laporan->id;
            $data['dosen_id'] = $laporan->dosen_id ?: $user->dosen_pembimbing_id;
            $data['status'] = 'pending';
        } else {
            $student = User::find($request->mahasiswa_id);
            $laporan = Laporan::where('mahasiswa_id', $request->mahasiswa_id)->latest()->first();
            if (!$laporan) {
                return back()->with('error', 'Mahasiswa yang dipilih tidak memiliki Laporan Akademik.');
            }
            $data['mahasiswa_id'] = $request->mahasiswa_id;
            $data['laporan_id'] = $laporan->id;
            $data['dosen_id'] = $laporan->dosen_id ?: $student->dosen_pembimbing_id;
            $data['status'] = $request->status ?? 'pending';
        }

        LaporanMingguan::create($data);

        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan berhasil ditambahkan.');
    }

    public function update(Request $request, LaporanMingguan $laporanMingguan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $rules = [
            'week' => 'required|integer|min:1',
            'isi' => 'required|string',
        ];

        if ($roleName === 'dosen') {
            $rules = [
                'status' => 'required|in:pending,disetujui,ditolak',
            ];
        } elseif (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['mahasiswa_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,disetujui,ditolak';
        }

        $request->validate($rules, [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'min' => ':attribute minimal bernilai :min.',
        ], [
            'week' => 'Minggu Ke',
            'isi' => 'Isi / Link Bimbingan',
            'mahasiswa_id' => 'Mahasiswa',
            'status' => 'Status',
        ]);

        if ($roleName === 'dosen') {
            $laporanMingguan->update([
                'status' => $request->status,
            ]);
        } elseif ($roleName === 'mahasiswa') {
            if ($laporanMingguan->mahasiswa_id !== $user->id) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengubah laporan ini.');
            }
            $laporanMingguan->update([
                'week' => $request->week,
                'isi' => $request->isi,
                'status' => 'pending', // reset status on edit
            ]);
        } else {
            $student = User::find($request->mahasiswa_id);
            $laporan = Laporan::where('mahasiswa_id', $request->mahasiswa_id)->latest()->first();
            if (!$laporan) {
                return back()->with('error', 'Mahasiswa yang dipilih tidak memiliki Laporan Akademik.');
            }
            $laporanMingguan->update([
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

    public function destroy(LaporanMingguan $laporanMingguan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        if ($roleName === 'mahasiswa' && $laporanMingguan->mahasiswa_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus laporan ini.');
        }

        $laporanMingguan->delete();

        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan berhasil dihapus.');
    }
}
