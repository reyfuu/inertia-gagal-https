@extends(backpack_view('layouts.plain'))

@section('content')
<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Premium CSS -->
<link rel="stylesheet" href="/css/premium.css?v={{ time() }}">
<!-- Line Awesome -->
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<style>
    /* Force background styles if CSS file fails */
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
    }
</style>

<div class="container d-flex align-items-center justify-content-center">
    <div class="auth-card col-12 col-sm-8 col-md-6 col-lg-4">
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
                <label class="form-label text-white">Email</label>
                <input type="text" class="form-control" name="email" value="{{ old(config('backpack.base.authentication_column_name')) }}" required autofocus style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255,255,255,0.1); color: white;">
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Sandi</label>
                <input type="password" class="form-control" name="password" required style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255,255,255,0.1); color: white;">
            </div>


            <button type="submit" class="btn w-100 mt-2" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border: none; color: white; padding: 12px; font-weight: 600; border-radius: 12px;">
                Login <i class="la la-arrow-right ms-2"></i>
            </button>

            <div class="text-center mt-3">
                <p class="text-muted small">Belum punya akun? <a href="{{ route('backpack.auth.register') }}" class="text-info text-decoration-none fw-bold">Daftar di sini</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
