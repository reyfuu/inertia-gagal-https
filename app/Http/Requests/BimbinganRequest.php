<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Request validator untuk data Bimbingan.
 * Mengatur hak akses dan aturan validasi pembuatan/pembaruan bimbingan.
 */
class BimbinganRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan untuk membuat request ini.
     * Menggunakan Auth::check() untuk memverifikasi pengguna yang terautentikasi.
     *
     * @return bool
     */
    public function authorize()
    {
        // Pengguna harus login terlebih dahulu
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
            'user_id' => 'required',
            'dosen_id' => 'required',
            'topik' => 'required|min:5',
            'tanggal' => 'required|date',
            'type' => 'required',
            'status' => 'required',
            'isi' => 'required',
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
            'required' => ':attribute wajib diisi.',
            'min' => ':attribute minimal :min karakter.',
            'date' => 'Format tanggal tidak valid.',
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
            'user_id' => 'Mahasiswa',
            'dosen_id' => 'Dosen',
            'topik' => 'Topik Pertemuan',
            'tanggal' => 'Tanggal',
            'type' => 'Jenis',
            'status' => 'Status',
            'isi' => 'Isi Bimbingan',
        ];
    }
}
