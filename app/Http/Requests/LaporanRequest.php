<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

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

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi.',
            'min' => ':attribute minimal :min karakter.',
            'date' => 'Format tanggal tidak valid.',
        ];
    }

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
