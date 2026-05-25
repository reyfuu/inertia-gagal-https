<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanMingguanRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'mahasiswa_id' => 'required',
            'week' => 'required|numeric|min:1',
            'isi' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal bernilai :min.',
        ];
    }

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
