<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bimbingan extends Model
{

    protected $fillable = [
        'topik',
        'status',
        'user_id',
        'dosen_id',
        'tanggal',
        'pertemuan_ke',
        'isi',
        'komentar',
        'type',
        'revision_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
