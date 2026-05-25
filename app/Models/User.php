<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'npm', 'nidn', 'status', 'angkatan', 'kategori', 'dosen_pembimbing_id', 'telegram_chat_id', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
                    ->withPivot('model_type');
    }

    public function getRoleIdAttribute()
    {
        return $this->roles->first()?->id;
    }

    public function setRoleIdAttribute($value)
    {
        if ($value) {
            $this->roles()->sync([
                $value => ['model_type' => self::class]
            ]);
        }
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = $value;
        }
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'mahasiswa_id');
    }

    public function bimbingans()
    {
        return $this->hasMany(Bimbingan::class, 'user_id');
    }

    public function laporanMingguans()
    {
        return $this->hasMany(LaporanMingguan::class, 'mahasiswa_id');
    }
}
