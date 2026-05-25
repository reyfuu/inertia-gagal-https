<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Laporan extends Model
{

    protected $fillable = [
        'judul',
        'tanggal_mulai',
        'tanggal_berakhir',
        'deskripsi',
        'mahasiswa_id',
        'dosen_id',
        'dokumen',
        'status',
        'komentar',
        'type',
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

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getMahasiswaNameAttribute()
    {
        return $this->mahasiswa ? $this->mahasiswa->name : '-';
    }

    public function getDosenNameAttribute()
    {
        return $this->dosen ? $this->dosen->name : '-';
    }

    public function getDokumenLinkAttribute()
    {
        if (!$this->dokumen) return '-';
        return '<a href="'.$this->dokumen.'" target="_blank" class="text-decoration-none"><i class="la la-external-link-alt"></i> Buka</a>';
    }

    public function getStatusBadgeAttribute()
    {
        $status = strtolower($this->status ?? '');
        $class = 'badge bg-secondary-lt';
        if ($status == 'disetujui') $class = 'badge bg-success-lt';
        if ($status == 'revisi')    $class = 'badge bg-danger-lt';
        if ($status == 'review')    $class = 'badge bg-warning-lt';
        
        return '<span class="'.$class.'">'.ucfirst($status ?: '-').'</span>';
    }

    public function getStatusHtml()
    {
        $status = strtolower($this->status ?? '');
        $class = 'badge bg-secondary-lt';
        if ($status == 'disetujui') $class = 'badge bg-success-lt';
        if ($status == 'revisi')    $class = 'badge bg-danger-lt';
        if ($status == 'review')    $class = 'badge bg-warning-lt';
        
        return '<span class="'.$class.'">'.ucfirst($status ?: '-').'</span>';
    }

    public function getDokumenHtml()
    {
        if (!$this->dokumen) return '-';
        return '<a href="'.$this->dokumen.'" target="_blank" class="text-decoration-none"><i class="la la-external-link-alt"></i> Buka</a>';
    }
}
