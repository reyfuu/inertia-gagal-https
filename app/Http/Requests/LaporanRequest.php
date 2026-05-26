<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Request validator untuk data Laporan Akademik.
 * Mengatur hak akses dan aturan validasi pembuatan/pembaruan laporan akademik.
 */
class LaporanRequest extends FormRequest
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
            'dosen_id' => 'required',
            'judul' => 'required|min:10',
            'type' => 'required',
            'dokumen' => 'required',
            'status' => 'required',
            'tanggal_mulai' => 'required|date',
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
            'mahasiswa_id' => 'Mahasiswa',
            'dosen_id' => 'Dosen Pembimbing',
            'judul' => 'Judul Laporan',
            'type' => 'Jenis Laporan',
            'dokumen' => 'Link Dokumen',
            'status' => 'Status',
            'tanggal_mulai' => 'Tanggal Mulai',
        ];
    }
}
