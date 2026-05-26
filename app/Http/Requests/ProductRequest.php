<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Request validator untuk data Product.
 * Mengatur hak akses dan aturan validasi pembuatan/pembaruan produk.
 */
class ProductRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan untuk membuat request ini.
     * Menggunakan Auth::check() untuk memverifikasi pengguna yang terautentikasi.
     *
     * @return bool
     */
    public function authorize()
    {
        // Hanya izinkan jika pengguna sudah masuk (terautentikasi)
        return Auth::check();
    }

    /**
     * Mendapatkan aturan validasi yang berlaku untuk request ini.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|min:5|max:255'
        ];
    }

    /**
     * Mendapatkan nama atribut yang ramah pengguna untuk kesalahan validasi.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Mendapatkan pesan kesalahan kustom untuk aturan validasi yang dilanggar.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
