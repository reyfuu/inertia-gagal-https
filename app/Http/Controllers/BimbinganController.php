<?php

namespace App\Http\Controllers;

use App\Models\Bimbingan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BimbinganController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $search = $request->input('search');

        $query = Bimbingan::with(['user:id,name', 'dosen:id,name'])
            ->when($search, function($q, $search) {
                $q->where('topik', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });

        // Filter based on roles
        if ($roleName === 'dosen') {
            $query->where('dosen_id', $user->id);
        } elseif ($roleName === 'mahasiswa') {
            $query->where('user_id', $user->id);
        } // Admin/Kaprodi can see all

        $bimbingans = $query->latest()->paginate(10)->withQueryString();

        // Get list of mahasiswas and dosens for create form
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

        return Inertia::render('Bimbingan/Index', [
            'bimbingans' => $bimbingans,
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
            'topik' => 'required|string|min:5|max:255',
            'tanggal' => 'required|date',
            'type' => 'required|in:skripsi,proposal',
            'isi' => 'required|string',
        ];

        // If admin, they must provide user_id and dosen_id
        if (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['user_id'] = 'required|exists:users,id';
            $rules['dosen_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:Review,Disetujui,Ditolak';
        }

        $request->validate($rules, [
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
        ]);

        $bimbinganData = [
            'topik' => $request->topik,
            'tanggal' => $request->tanggal,
            'type' => $request->type,
            'isi' => $request->isi,
        ];

        if ($roleName === 'mahasiswa') {
            $bimbinganData['user_id'] = $user->id;
            $bimbinganData['dosen_id'] = $user->dosen_pembimbing_id;
            $bimbinganData['status'] = 'Review';
            $bimbinganData['revision_count'] = Bimbingan::where('user_id', $user->id)->count() + 1;
        } else {
            $bimbinganData['user_id'] = $request->user_id;
            $bimbinganData['dosen_id'] = $request->dosen_id;
            $bimbinganData['status'] = $request->status ?? 'Review';
            $bimbinganData['komentar'] = $request->komentar;
            $bimbinganData['revision_count'] = Bimbingan::where('user_id', $request->user_id)->count() + 1;
        }

        Bimbingan::create($bimbinganData);

        return redirect()->route('bimbingan.index')->with('success', 'Bimbingan berhasil ditambahkan.');
    }

    public function update(Request $request, Bimbingan $bimbingan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $rules = [
            'topik' => 'required|string|min:5|max:255',
            'tanggal' => 'required|date',
            'type' => 'required|in:skripsi,proposal',
            'isi' => 'required|string',
        ];

        if ($roleName === 'dosen') {
            // Dosen updates comments and status
            $rules = [
                'status' => 'required|in:Review,Disetujui,Ditolak',
                'komentar' => 'nullable|string',
            ];
        } elseif (in_array($roleName, ['super_admin', 'ka_prodi'])) {
            $rules['user_id'] = 'required|exists:users,id';
            $rules['dosen_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:Review,Disetujui,Ditolak';
            $rules['komentar'] = 'nullable|string';
        }

        $request->validate($rules, [
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
        ]);

        if ($roleName === 'dosen') {
            $bimbingan->update([
                'status' => $request->status,
                'komentar' => $request->komentar,
            ]);
        } elseif ($roleName === 'mahasiswa') {
            // Students can only update their own review/rejected bimbingans
            if ($bimbingan->user_id !== $user->id) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengubah bimbingan ini.');
            }
            $bimbingan->update([
                'topik' => $request->topik,
                'tanggal' => $request->tanggal,
                'type' => $request->type,
                'isi' => $request->isi,
                'status' => 'Review', // Reset status back to review on edit
            ]);
        } else {
            // Admin can update everything
            $bimbingan->update([
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

    public function komentarHistory(Bimbingan $bimbingan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $bimbingan->load('user:id,name');

        if ($roleName === 'dosen' && $bimbingan->dosen_id !== $user->id) {
            abort(403);
        }

        if ($roleName === 'mahasiswa' && $bimbingan->user_id !== $user->id) {
            abort(403);
        }

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

        return response()->json([
            'mahasiswa_name' => $bimbingan->user?->name,
            'comments' => $comments,
        ]);
    }

    public function destroy(Bimbingan $bimbingan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        if ($roleName === 'mahasiswa' && $bimbingan->user_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus bimbingan ini.');
        }

        $bimbingan->delete();

        return redirect()->route('bimbingan.index')->with('success', 'Bimbingan berhasil dihapus.');
    }
}
