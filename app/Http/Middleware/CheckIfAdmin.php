<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware untuk memverifikasi apakah pengguna terautentikasi adalah Admin/Kaprodi.
 * Menghalangi akses non-admin dari rute administratif.
 */
class CheckIfAdmin
{
    /**
     * Memeriksa apakah pengguna memiliki salah satu role administratif.
     *
     * @param  ?\Illuminate\Contracts\Auth\Authenticatable  $user
     * @return bool
     */
    private function checkIfUserIsAdmin($user)
    {
        // Jika tidak ada data user, tolak
        if (!$user) {
            return false;
        }
        // Mendapatkan nama role pertama dari relasi roles
        $roleName = $user->roles->first()?->name;
        // Izinkan jika memiliki role super_admin atau ka_prodi
        return in_array($roleName, ['super_admin', 'ka_prodi']);
    }

    /**
     * Merespon request yang tidak terotorisasi.
     * Mengembalikan pesan JSON/Plain Text jika request berupa AJAX,
     * atau mengalihkan ke halaman login jika request biasa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function respondToUnauthorizedRequest($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response('Unauthorized.', 401);
        } else {
            return redirect()->guest(route('login'));
        }
    }

    /**
     * Menangani request masuk.
     * Memastikan pengguna sudah login dan merupakan administrator.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Jika belum terautentikasi (guest), tangani sebagai unauthorized request
        if (Auth::guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        // Jika bukan admin/kaprodi, tangani sebagai unauthorized request
        if (! $this->checkIfUserIsAdmin(Auth::user())) {
            return $this->respondToUnauthorizedRequest($request);
        }

        // Lanjutkan request ke tahapan berikutnya jika lolos verifikasi
        return $next($request);
    }
}
