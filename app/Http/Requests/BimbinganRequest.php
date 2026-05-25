<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BimbinganRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

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
