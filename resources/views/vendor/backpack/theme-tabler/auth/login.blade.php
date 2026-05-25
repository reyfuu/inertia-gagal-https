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
            radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.25) 0%, transparent 70%),
            radial-gradient(circle at 85% 85%, rgba(168, 85, 247, 0.25) 0%, transparent 70%) !important;
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
        border-radius: 32px !important;
        padding: 60px !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37) !important;
        width: 100% !important;
        max-width: 700px !important;
        margin: auto;
    }
    .form-control {
        background: rgba(15, 23, 42, 0.5) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: white !important;
        border-radius: 14px !important;
        padding: 15px 20px !important;
        font-size: 1.2rem !important;
    }
    .form-control:focus {
        background: rgba(15, 23, 42, 0.7) !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
    }
    .form-label {
        color: rgba(255,255,255,0.9);
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 12px;
    }
</style>

<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; width: 100%;">
    <div class="auth-card">
        <div class="text-center mb-4">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border-radius: 16px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                <i class="la la-bolt" style="color: white; font-size: 32px;"></i>
            </div>
            <h2 class="fw-bold text-white">Welcome Back</h2>
            <p class="text-muted">Sign in to TAMP</p>
        </div>

        <form method="POST" action="{{ route('backpack.auth.login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" name="email" value="{{ old(config('backpack.base.authentication_column_name')) }}" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Sandi</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <button type="submit" class="btn w-100 mt-3" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border: none; color: white; padding: 12px; font-weight: 600; border-radius: 12px;">
                Login <i class="la la-arrow-right ms-2"></i>
            </button>

            <div class="text-center mt-3">
                <p class="text-muted small">Belum punya akun? <a href="{{ route('backpack.auth.register') }}" class="text-info text-decoration-none fw-bold">Daftar di sini</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
