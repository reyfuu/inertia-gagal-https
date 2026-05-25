@extends(backpack_view('layouts.top_left'))

@section('header')
    <section class="container-fluid">
      <h1>
        <span class="text-capitalize">Dashboard</span>
        <small>Overview and stats.</small>
      </h1>
    </section>
@endsection

@section('content')
<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Premium CSS -->
<link rel="stylesheet" href="/css/premium.css?v={{ time() }}">
<!-- Line Awesome -->
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .glass-card {
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .banner-card {
            background: #ef4444; /* Solid red to match the image closely */
            border-radius: 12px;
            padding: 24px;
            color: white;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }
        .icon-blue { background: #3b82f6; color: white; }
        .icon-green { background: #22c55e; color: white; }
        .icon-yellow { background: #eab308; color: white; }
        .icon-red { background: #ef4444; color: white; }
        
        .stat-card {
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 24px;
            color: white;
            height: 100%;
        }
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .stat-desc {
            font-size: 13px;
            color: #94a3b8;
        }
        
        /* Specific borders based on image */
        .card-blue { border-top: 4px solid #14b8a6; } /* The image shows teal/cyan for the first */
        .card-green { border-top: 4px solid #22c55e; }
        .card-yellow { border-top: 4px solid #eab308; }
        .card-red { border-top: 4px solid #ef4444; }

        .banner-card p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 15px;
        }
        
        .icon-box-small {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;1
            justify-content: center;
            font-size: 18px;
        }
    </style>

    <div class="banner-card">
        <h3 class="fw-bold mb-2">👋 Selamat Datang, Admin</h3>
        <p class="mb-0">Dashboard Administrasi Monitoring</p>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-md-3">
            <div class="stat-card card-blue">
                <div class="icon-box icon-blue">
                    <i class="la la-user-friends"></i>
                </div>
                <div class="stat-label">TOTAL PENGGUNA SISTEM</div>
                <div class="stat-value">33</div>
                <div class="stat-desc">Semua pengguna aktif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-green">
                <div class="icon-box icon-green">
                    <i class="la la-check-square"></i>
                </div>
                <div class="stat-label">MAHASISWA ON TRACK</div>
                <div class="stat-value">1</div>
                <div class="stat-desc">4% dari total</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-yellow">
                <div class="icon-box icon-yellow">
                    <i class="la la-exclamation-triangle"></i>
                </div>
                <div class="stat-label">MAHASISWA AT RISK</div>
                <div class="stat-value">2</div>
                <div class="stat-desc">7% dari total</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-red">
                <div class="icon-box icon-red">
                    <i class="la la-dot-circle"></i>
                </div>
                <div class="stat-label">MAHASISWA OVERDUE</div>
                <div class="stat-value">25</div>
                <div class="stat-desc">89% dari total</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="glass-card h-100">
                <h5 class="mb-4 d-flex align-items-center gap-3">
                    <div class="icon-box-small icon-red">
                        <i class="la la-users"></i>
                    </div>
                    Beban Kerja Dosen
                </h5>
                <div style="height: 300px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                    <!-- Chart Placeholder -->
                    <span>Chart Beban Kerja Dosen</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card h-100">
                <h5 class="mb-4 d-flex align-items-center gap-3">
                    <div class="icon-box-small icon-red" style="background: white; color: #ef4444;">
                        <i class="la la-chart-bar"></i>
                    </div>
                    Statistik Ringkas
                </h5>
                <div style="height: 300px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                    <!-- Chart Placeholder -->
                    <span>Chart Statistik Ringkas</span>
                </div>
            </div>
        </div>
    </div>
@endsection
