<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $search = $request->input('search');

        $query = Laporan::with(['mahasiswa', 'dosen'])
            ->when($search, function($q, $search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function($qm) use ($search) {
                      $qm->where('name', 'like', "%{$search}%");
                  });
            });

        // Filter based on roles
        if ($roleName === 'dosen') {
            $query->where('dosen_id', $user->id);
        } elseif ($roleName === 'mahasiswa') {
            $query->where('mahasiswa_id', $user->id);
        }

        $laporans = $query->latest()->paginate(10)->withQueryString();

        $mahasiswas = [];
        $dosens = [];

        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $mahasiswas = User::whereHas('roles', function($q) {
                $q->where('name', 'mahasiswa');
            })->get();
            $dosens = User::whereNotNull('nidn')->orWhereHas('roles', function($q) {
                $q->whereIn('name', ['dosen', 'ka_prodi']);
            })->get();
        }

        return Inertia::render('Laporan/Index', [
            'laporans' => $laporans,
            'mahasiswas' => $mahasiswas,
            'dosens' => $dosens,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
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

        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['mahasiswa_id'] = 'required|exists:users,id';
            $rules['dosen_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,disetujui,revisi';
        }

        $request->validate($rules, [
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
        ]);

        $laporanData = [
            'judul' => $request->judul,
            'type' => $request->type,
            'dokumen' => $request->dokumen,
            'tanggal_mulai' => $request->tanggal_mulai,
            'deskripsi' => $request->deskripsi,
        ];

        if ($roleName === 'mahasiswa') {
            $laporanData['mahasiswa_id'] = $user->id;
            $laporanData['dosen_id'] = $user->dosen_pembimbing_id;
            $laporanData['status'] = 'pending';
        } else {
            $laporanData['mahasiswa_id'] = $request->mahasiswa_id;
            $laporanData['dosen_id'] = $request->dosen_id;
            $laporanData['status'] = $request->status ?? 'pending';
            $laporanData['komentar'] = $request->komentar;
        }

        Laporan::create($laporanData);

        return redirect()->route('laporan.index')->with('success', 'Laporan akademik berhasil ditambahkan.');
    }

    public function update(Request $request, Laporan $laporan)
    {
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

        if ($roleName === 'dosen') {
            $rules = [
                'status' => 'required|in:pending,disetujui,revisi',
                'komentar' => 'nullable|string',
            ];
        } elseif (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['mahasiswa_id'] = 'required|exists:users,id';
            $rules['dosen_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,disetujui,revisi';
            $rules['komentar'] = 'nullable|string';
        }

        $request->validate($rules, [
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
        ]);

        if ($roleName === 'dosen') {
            $laporan->update([
                'status' => $request->status,
                'komentar' => $request->komentar,
            ]);
        } elseif ($roleName === 'mahasiswa') {
            if ($laporan->mahasiswa_id !== $user->id) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengubah laporan ini.');
            }
            $laporan->update([
                'judul' => $request->judul,
                'type' => $request->type,
                'dokumen' => $request->dokumen,
                'tanggal_mulai' => $request->tanggal_mulai,
                'deskripsi' => $request->deskripsi,
                'status' => 'pending', // reset status on edit
            ]);
        } else {
            $laporan->update([
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

    public function destroy(Laporan $laporan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        if ($roleName === 'mahasiswa' && $laporan->mahasiswa_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus laporan ini.');
        }

        $laporan->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan akademik berhasil dihapus.');
    }
}
