@extends(backpack_view('layouts.plain'))

@section('content')
<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Line Awesome -->
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<style>
    body {
        background-color: #0f172a !important;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.15) 0%, transparent 40%) !important;
        color: white !important;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .auth-card {
        background: rgba(30, 41, 59, 0.7) !important;
        backdrop-filter: blur(12px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 24px !important;
        padding: 40px !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37) !important;
        margin: 20px 0;
    }
    .form-control, .form-select {
        background: rgba(15, 23, 42, 0.5) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: white !important;
        border-radius: 12px !important;
    }
    .form-control:focus, .form-select:focus {
        background: rgba(15, 23, 42, 0.7) !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
    }
    .form-label {
        color: rgba(255,255,255,0.8);
        font-weight: 500;
    }
</style>

<div class="container d-flex align-items-center justify-content-center">
    <div class="auth-card col-12 col-sm-10 col-md-8 col-lg-6">
        <div class="text-center mb-4">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border-radius: 16px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                <i class="la la-user-plus" style="color: white; font-size: 32px;"></i>
            </div>
            <h2 class="fw-bold text-white">Daftar Akun Baru</h2>
            <p class="text-muted">Lengkapi data diri Anda untuk mendaftar</p>
        </div>

        <form method="POST" action="{{ route('backpack.auth.register') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NPM</label>
                    <input type="text" class="form-control" name="npm" value="{{ old('npm') }}" required autofocus>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telegram ID</label>
                    <input type="text" class="form-control" name="telegram_chat_id" value="{{ old('telegram_chat_id') }}" placeholder="Contoh: 12345678" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori Mahasiswa</label>
                <select class="form-select" name="kategori" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="magang">Magang</option>
                    <option value="skripsi">Skripsi</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>
            </div>

            <button type="submit" class="btn w-100 mt-3" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border: none; color: white; padding: 12px; font-weight: 600; border-radius: 12px;">
                Daftar Sekarang <i class="la la-check-circle ms-2"></i>
            </button>
            
            <div class="text-center mt-3">
                <p class="text-muted small">Sudah punya akun? <a href="{{ route('backpack.auth.login') }}" class="text-info text-decoration-none fw-bold">Login di sini</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
