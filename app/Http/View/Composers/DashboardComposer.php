<?php

namespace App\Http\View\Composers;

use App\Models\Bimbingan;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardComposer
{
    public function compose(View $view)
    {
        // 1. Data Beban Kerja Dosen
        $bebanDosen = User::whereNotNull('nidn')
            ->get()
            ->map(function($dosen) {
                $totalMhs = Bimbingan::where('dosen_id', $dosen->id)->distinct('user_id')->count('user_id');
                return [
                    'label' => $dosen->name,
                    'value' => $totalMhs,
                    'status' => $totalMhs > 15 ? 'Sibuk' : 'Baik',
                    'status_class' => $totalMhs > 15 ? 'bg-warning-lt' : 'bg-success-lt'
                ];
            });

        // 2. Counter Utama (Fokus pada DATA LAPORAN sesuai permintaan user)
        $totalLaporan = Laporan::count();
        $onTrackCount = Laporan::where('status', 'disetujui')->count();
        $atRiskCount = Laporan::whereIn('status', ['revisi', 'review', 'Review', 'Revisi'])->count();
        $overdueCount = Laporan::where('status', 'pending')->count();
        
        // Jika total 0, pastikan tidak error (walaupun count() aman)
        // Kita juga tambahkan total Mahasiswa untuk konteks
        $totalMahasiswa = User::whereNotNull('npm')->count();

        // 3. Statistik Ringkas
        $totalDosen = User::whereNotNull('nidn')->count();
        $totalBimbingan = Bimbingan::count();
        $bimbinganSelesai = Bimbingan::where('status', 'disetujui')->count();
        $bimbinganReview = Bimbingan::whereIn('status', ['revisi', 'review', 'Review', 'Revisi'])->count();
        
        $laporanProposal = Laporan::where('type', 'proposal')->count();
        $laporanMagang = Laporan::where('type', 'magang')->count();
        $laporanSkripsi = Laporan::where('type', 'skripsi')->count();

        $view->with([
            'bebanDosen' => $bebanDosen,
            'totalUsers' => $totalLaporan, // Kita ganti jadi total laporan agar user melihat datanya
            'onTrackCount' => $onTrackCount,
            'atRiskCount' => $atRiskCount,
            'overdueCount' => $overdueCount,
            // Statistik Ringkas data
            'totalMahasiswa' => $totalMahasiswa,
            'totalDosen' => $totalDosen,
            'totalBimbingan' => $totalBimbingan,
            'bimbinganSelesai' => $bimbinganSelesai,
            'bimbinganReview' => $bimbinganReview,
            'laporanProposal' => $laporanProposal,
            'laporanMagang' => $laporanMagang,
            'laporanSkripsi' => $laporanSkripsi,
        ]);
    }
}
