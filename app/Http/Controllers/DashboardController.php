<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bimbingan;
use App\Models\Laporan;
use App\Models\LaporanMingguan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roleName = $user->roles->first()?->name;

        $stats = [
            'total_users' => 0,
            'total_mahasiswa' => 0,
            'total_dosen' => 0,
            'total_bimbingan' => 0,
            'total_laporan' => 0,
            'total_laporan_mingguan' => 0,
        ];

        $recentBimbingan = [];
        $recentLaporan = [];

        if ($roleName === 'super_admin' || $roleName === 'ka_prodi') {
            // Admin / Kaprodi see all
            $stats['total_users'] = User::count();
            $stats['total_mahasiswa'] = User::whereHas('roles', function($q) { $q->where('name', 'mahasiswa'); })->count();
            $stats['total_dosen'] = User::whereHas('roles', function($q) { $q->where('name', 'dosen'); })->count();
            $stats['total_bimbingan'] = Bimbingan::count();
            $stats['total_laporan'] = Laporan::count();
            $stats['total_laporan_mingguan'] = LaporanMingguan::count();

            $recentBimbingan = Bimbingan::with(['user', 'dosen'])->latest()->limit(5)->get();
            $recentLaporan = Laporan::with(['mahasiswa', 'dosen'])->latest()->limit(5)->get();
        } elseif ($roleName === 'dosen') {
            // Dosen sees their students and their own activities
            $studentIds = User::where('dosen_pembimbing_id', $user->id)->pluck('id')->toArray();
            
            $stats['total_mahasiswa'] = count($studentIds);
            $stats['total_bimbingan'] = Bimbingan::where('dosen_id', $user->id)->count();
            $stats['total_laporan'] = Laporan::where('dosen_id', $user->id)->count();
            $stats['total_laporan_mingguan'] = LaporanMingguan::where('dosen_id', $user->id)->count();

            $recentBimbingan = Bimbingan::with(['user', 'dosen'])
                ->where('dosen_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();

            $recentLaporan = Laporan::with(['mahasiswa', 'dosen'])
                ->where('dosen_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        } else {
            // Mahasiswa sees their own
            $stats['total_bimbingan'] = Bimbingan::where('user_id', $user->id)->count();
            $stats['total_laporan'] = Laporan::where('mahasiswa_id', $user->id)->count();
            $stats['total_laporan_mingguan'] = LaporanMingguan::where('mahasiswa_id', $user->id)->count();

            $recentBimbingan = Bimbingan::with(['user', 'dosen'])
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();

            $recentLaporan = Laporan::with(['mahasiswa', 'dosen'])
                ->where('mahasiswa_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentBimbingan' => $recentBimbingan,
            'recentLaporan' => $recentLaporan,
        ]);
    }
}
