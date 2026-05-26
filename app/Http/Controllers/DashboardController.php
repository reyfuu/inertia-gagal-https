<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bimbingan;
use App\Models\Laporan;
use App\Models\LaporanMingguan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Controller untuk mengelola halaman Dashboard Utama.
 * Menghitung statistik dan data aktivitas terbaru berdasarkan peran (role) pengguna.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan statistik yang sesuai dengan role pengguna saat ini.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Mendapatkan objek user yang sedang login saat ini
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Mengambil nama role pertama dari relasi roles user
        $roleName = $user->roles->first()?->name;

        // Inisialisasi awal statistik data
        $stats = [
            'total_users' => 0,
            'total_mahasiswa' => 0,
            'total_dosen' => 0,
            'total_bimbingan' => 0,
            'total_laporan' => 0,
            'total_laporan_mingguan' => 0,
        ];

        // Inisialisasi awal untuk daftar aktivitas terbaru
        $recentBimbingan = [];
        $recentLaporan = [];

        // Logika statistik dan data berdasarkan peran (role)
        if ($roleName === 'super_admin' || $roleName === 'ka_prodi') {
            // Admin dan Kaprodi memiliki hak akses untuk melihat keseluruhan data statistik
            $stats['total_users'] = User::query()->count();
            $stats['total_mahasiswa'] = User::query()->whereHas('roles', function($q) { $q->where('name', 'mahasiswa'); })->count();
            $stats['total_dosen'] = User::query()->whereHas('roles', function($q) { $q->where('name', 'dosen'); })->count();
            $stats['total_bimbingan'] = Bimbingan::query()->count();
            $stats['total_laporan'] = Laporan::query()->count();
            $stats['total_laporan_mingguan'] = LaporanMingguan::query()->count();

            // Mengambil 5 aktivitas bimbingan dan laporan terbaru secara global
            $recentBimbingan = Bimbingan::query()->with(['user', 'dosen'])->latest()->limit(5)->get();
            $recentLaporan = Laporan::query()->with(['mahasiswa', 'dosen'])->latest()->limit(5)->get();
        } elseif ($roleName === 'dosen') {
            // Dosen hanya dapat melihat data mahasiswa bimbingannya dan aktivitas yang terkait dengannya
            $studentIds = User::query()->where('dosen_pembimbing_id', $user->id)->pluck('id')->toArray();
            
            $stats['total_mahasiswa'] = count($studentIds);
            $stats['total_bimbingan'] = Bimbingan::query()->where('dosen_id', $user->id)->count();
            $stats['total_laporan'] = Laporan::query()->where('dosen_id', $user->id)->count();
            $stats['total_laporan_mingguan'] = LaporanMingguan::query()->where('dosen_id', $user->id)->count();

            // Mengambil 5 aktivitas bimbingan dan laporan terbaru milik dosen yang bersangkutan
            $recentBimbingan = Bimbingan::query()->with(['user', 'dosen'])
                ->where('dosen_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();

            $recentLaporan = Laporan::query()->with(['mahasiswa', 'dosen'])
                ->where('dosen_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        } else {
            // Mahasiswa hanya dapat melihat data statistik pribadinya sendiri
            $stats['total_bimbingan'] = Bimbingan::query()->where('user_id', $user->id)->count();
            $stats['total_laporan'] = Laporan::query()->where('mahasiswa_id', $user->id)->count();
            $stats['total_laporan_mingguan'] = LaporanMingguan::query()->where('mahasiswa_id', $user->id)->count();

            // Mengambil 5 aktivitas bimbingan dan laporan terbaru milik mahasiswa yang bersangkutan
            $recentBimbingan = Bimbingan::query()->with(['user', 'dosen'])
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();

            $recentLaporan = Laporan::query()->with(['mahasiswa', 'dosen'])
                ->where('mahasiswa_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        // Merender view Dashboard dengan mengirimkan data statistik dan aktivitas terbaru
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentBimbingan' => $recentBimbingan,
            'recentLaporan' => $recentLaporan,
        ]);
    }
}
