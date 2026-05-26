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
        $bebanDosen = User::query()->where('nidn', '!=', '')
            ->get()
            ->map(function($dosen) {
                $totalMhs = Bimbingan::query()->select('user_id')->where('dosen_id', $dosen->id)->get()->pluck('user_id')->unique()->count();
                return [
                    'label' => $dosen->name,
                    'value' => $totalMhs,
                    'status' => $totalMhs > 15 ? 'Sibuk' : 'Baik',
                    'status_class' => $totalMhs > 15 ? 'bg-warning-lt' : 'bg-success-lt'
                ];
            });

        // 2. Counter Utama (Fokus pada DATA LAPORAN sesuai permintaan user)
        $totalLaporan = Laporan::query()->select('id')->get()->count();
        $onTrackCount = Laporan::query()->select('id')->where('status', 'disetujui')->get()->count();
        $atRiskCount = Laporan::query()->select('id')->where(function($q) {
            $q->where('status', 'revisi')
              ->orWhere('status', 'review')
              ->orWhere('status', 'Review')
              ->orWhere('status', 'Revisi');
        })->get()->count();
        $overdueCount = Laporan::query()->select('id')->where('status', 'pending')->get()->count();
        
        // Jika total 0, pastikan tidak error (walaupun count() aman)
        // Kita juga tambahkan total Mahasiswa untuk konteks
        $totalMahasiswa = User::query()->select('id')->where('npm', '!=', '')->get()->count();

        // 3. Statistik Ringkas
        $totalDosen = User::query()->select('id')->where('nidn', '!=', '')->get()->count();
        $totalBimbingan = Bimbingan::query()->select('id')->get()->count();
        $bimbinganSelesai = Bimbingan::query()->select('id')->where('status', 'disetujui')->get()->count();
        $bimbinganReview = Bimbingan::query()->select('id')->where(function($q) {
            $q->where('status', 'revisi')
              ->orWhere('status', 'review')
              ->orWhere('status', 'Review')
              ->orWhere('status', 'Revisi');
        })->get()->count();
        
        $laporanProposal = Laporan::query()->select('id')->where('type', 'proposal')->get()->count();
        $laporanMagang = Laporan::query()->select('id')->where('type', 'magang')->get()->count();
        $laporanSkripsi = Laporan::query()->select('id')->where('type', 'skripsi')->get()->count();

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
