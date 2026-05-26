<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string|null $topik
 * @property string|null $status
 * @property int $user_id
 * @property int|null $dosen_id
 * @property string|null $tanggal
 * @property int|null $pertemuan_ke
 * @property string|null $isi
 * @property string|null $komentar
 * @property string $type
 * @property int $revision_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $dosen
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereDosenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereIsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereKomentar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan wherePertemuanKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereRevisionCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereTopik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bimbingan whereUserId($value)
 */
	class Bimbingan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $judul
 * @property string $tanggal_mulai
 * @property string|null $tanggal_berakhir
 * @property string|null $deskripsi
 * @property int|null $mahasiswa_id
 * @property int|null $dosen_id
 * @property string|null $dokumen
 * @property string|null $status
 * @property string|null $komentar
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $dosen
 * @property-read mixed $dokumen_link
 * @property-read mixed $dosen_name
 * @property-read mixed $mahasiswa_name
 * @property-read mixed $status_badge
 * @property-read \App\Models\User|null $mahasiswa
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereDokumen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereDosenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKomentar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereMahasiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereTanggalBerakhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereUpdatedAt($value)
 */
	class Laporan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $laporan_id
 * @property int|null $mahasiswa_id
 * @property int|null $dosen_id
 * @property int|null $week
 * @property string|null $isi
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $dosen
 * @property-read \App\Models\User|null $mahasiswa
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereDosenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereIsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereLaporanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereMahasiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanMingguan whereWeek($value)
 */
	class LaporanMingguan extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $telegram_chat_id
 * @property string $password
 * @property string|null $npm
 * @property string|null $nidn
 * @property string $status
 * @property string|null $angkatan
 * @property string|null $kategori
 * @property int|null $dosen_pembimbing_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bimbingan> $bimbingans
 * @property-read int|null $bimbingans_count
 * @property-read User|null $dosenPembimbing
 * @property mixed $role_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LaporanMingguan> $laporanMingguans
 * @property-read int|null $laporan_mingguans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Laporan> $laporans
 * @property-read int|null $laporans_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAngkatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDosenPembimbingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNidn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNpm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelegramChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

