<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Request validator untuk data Laporan Mingguan.
 * Mengatur hak akses dan aturan validasi pembuatan/pembaruan laporan mingguan.
 */
class LaporanMingguanRequest extends FormRequest
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
            'mahasiswa_id' => 'required',
            'week' => 'required|numeric|min:1',
            'isi' => 'required',
            'status' => 'required',
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
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal bernilai :min.',
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
            'mahasiswa_id' => 'Mahasiswa',
            'week' => 'Minggu Ke',
            'isi' => 'Link Dokumen / Isi',
            'status' => 'Status',
        ];
    }
}
