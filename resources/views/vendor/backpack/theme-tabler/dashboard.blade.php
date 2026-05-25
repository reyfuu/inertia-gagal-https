@extends(backpack_view('layouts.vertical'))

@section('header')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="mb-0 text-white">Dashboard <small class="text-muted fs-4">Overview and stats</small></h1>
</div>
<!-- Line Awesome -->
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Kartu Banner Sambutan (Gradient Premium) */
        .banner-card {
            background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%); 
            border-radius: 16px;
            padding: 30px;
            color: white;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .banner-card::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Tipografi Nilai Statistik (Angka Besar) */
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        /* Tipografi Label Statistik (Teks Kecil di Atas) */
        .stat-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        /* Deskripsi Tambahan di Bawah Angka */
        .stat-desc {
            font-size: 13px;
            color: #94a3b8;
        }
        
        /* Indikator Garis Berwarna di Bagian Atas Kartu */
        .card-blue { border-top: 4px solid #14b8a6 !important; }
        .card-green { border-top: 4px solid #22c55e !important; }
        .card-yellow { border-top: 4px solid #eab308 !important; }
        .card-red { border-top: 4px solid #ef4444 !important; }

        /* Teks dalam Banner Sambutan */
        .banner-card p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 15px;
        }

        /* Custom Dosen Card */
        .dosen-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        .dosen-card:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.05);
        }

        /* Ringkas Stats Card */
        .ringkas-card {
            background: rgba(30, 41, 59, 0.4);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #6366f1;
        }
    </style>

    <div class="banner-card d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-2">👋 Selamat Datang Kembali, Admin</h2>
            <p class="mb-0 opacity-75">Sistem Monitoring Tugas Akhir & Magang (TAMP)</p>
        </div>
        <div class="d-none d-md-block">
            <i class="la la-rocket" style="font-size: 80px; opacity: 0.2; transform: rotate(15deg);"></i>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-md-3">
            <div class="stat-card card-blue">
                <div class="stat-label">TOTAL LAPORAN</div>
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-desc">Semua laporan masuk</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-green">
                <div class="stat-label">LAPORAN DISETUJUI</div>
                <div class="stat-value">{{ $onTrackCount }}</div>
                <div class="stat-desc">Selesai diperiksa</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-yellow">
                <div class="stat-label">LAPORAN REVISI/REVIEW</div>
                <div class="stat-value">{{ $atRiskCount }}</div>
                <div class="stat-desc">Perlu perbaikan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-red">
                <div class="stat-label">LAPORAN PENDING</div>
                <div class="stat-value">{{ $overdueCount }}</div>
                <div class="stat-desc">Menunggu antrean</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="glass-card">
                <h5 class="mb-4 d-flex align-items-center gap-2">
                    <i class="la la-users text-primary"></i> Beban Kerja Dosen
                </h5>
                <div class="row">
                    @foreach($bebanDosen as $dosen)
                    <div class="col-12">
                        <div class="dosen-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1 text-white">{{ $dosen['label'] }}</h5>
                                    <span class="text-muted small">Mahasiswa: <strong>{{ $dosen['value'] }} mhs</strong></span>
                                </div>
                                <span class="badge {{ $dosen['status_class'] }}">{{ $dosen['status'] }}</span>
                            </div>
                            <div class="progress progress-sm" style="height: 6px; background: rgba(255,255,255,0.05);">
                                <div class="progress-bar bg-primary" style="width: {{ min(($dosen['value'] / 20) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card">
                <h5 class="mb-4 d-flex align-items-center gap-2">
                    <i class="la la-chart-bar text-danger"></i> Statistik Ringkas
                </h5>
                
                <div class="ringkas-card" style="border-left-color: #3b82f6;">
                    <div class="stat-label text-blue">Total Mahasiswa</div>
                    <div class="h2 mb-0 fw-bold">{{ $totalMahasiswa }}</div>
                </div>

                <div class="ringkas-card" style="border-left-color: #10b981;">
                    <div class="stat-label text-green">Total Dosen Pembimbing</div>
                    <div class="h2 mb-0 fw-bold">{{ $totalDosen }}</div>
                </div>

                <div class="ringkas-card" style="border-left-color: #6366f1;">
                    <div class="stat-label text-indigo">Total Bimbingan</div>
                    <div class="h2 mb-0 fw-bold">{{ $totalBimbingan }}</div>
                </div>

                <div class="ringkas-card" style="border-left-color: #f59e0b;">
                    <div class="stat-label text-warning">Bimbingan Selesai</div>
                    <div class="h2 mb-0 fw-bold">{{ $bimbinganSelesai }}</div>
                </div>

                <div class="ringkas-card" style="border-left-color: #3b82f6;">
                    <div class="stat-label text-blue">Bimbingan Review</div>
                    <div class="h2 mb-0 fw-bold">{{ $bimbinganReview }}</div>
                </div>

                <div class="ringkas-card" style="border-left-color: #10b981;">
                    <div class="stat-label text-green">Laporan Proposal</div>
                    <div class="h2 mb-0 fw-bold">{{ $laporanProposal }}</div>
                </div>

                <div class="ringkas-card" style="border-left-color: #0d9488;">
                    <div class="stat-label text-teal">Laporan Magang</div>
                    <div class="h2 mb-0 fw-bold">{{ $laporanMagang }}</div>
                </div>

                <div class="ringkas-card" style="border-left-color: #0f172a; border-left-width: 4px;">
                    <div class="stat-label text-white">Laporan Skripsi</div>
                    <div class="h2 mb-0 fw-bold">{{ $laporanSkripsi }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
