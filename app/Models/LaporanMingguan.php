<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LaporanMingguan extends Model
{

    protected $fillable = [
        'laporan_id',
        'mahasiswa_id',
        'dosen_id',
        'week',
        'isi',
        'status',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function getIsiLinkHtml()
    {
        if (!$this->isi) return '-';
        return '<a href="'.$this->isi.'" target="_blank" class="text-decoration-none">Buka Dokumen</a>';
    }
}
