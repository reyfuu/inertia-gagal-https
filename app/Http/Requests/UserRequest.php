<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        // Ambil ID dari route parameter (standar Backpack)
        $userId = $this->route('id');
        
        return [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . ($userId ?: 'NULL'),
            'password' => $this->method() == 'POST' ? 'required|min:8|confirmed' : 'nullable|min:8|confirmed',
            'role_id' => 'required',
            'status' => 'required',
            // Gunakan nullable agar tidak bentrok dengan visibilitas JS, 
            // validasi required_if kita tangani dengan logika yang lebih luwes
            'npm' => 'sometimes',
            'nidn' => 'sometimes',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi, tidak boleh kosong.',
            'email' => 'Format email tidak valid.',
            'unique' => ':attribute sudah terdaftar di sistem.',
            'min' => ':attribute minimal harus :min karakter.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama Lengkap',
            'email' => 'Alamat Email',
            'password' => 'Password',
            'roles' => 'Role/Peran',
            'status' => 'Status Akun',
            'npm' => 'NPM',
            'nidn' => 'NIDN',
        ];
    }
}
