<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;
    protected $guarded = ['id'];

    const ROLE_ADMIN = 'admin';
    const ROLE_KECAMATAN = 'kecamatan';
    const ROLE_NAGARI = 'nagari';
    const ROLE_PELATIH = 'pelatih';
    const ROLE_ATLET = 'atlet';

    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_KECAMATAN,
            self::ROLE_NAGARI,
            self::ROLE_PELATIH,
            self::ROLE_ATLET
        ];
    }

    // Relasi ke data yang dibuat oleh user
    public function atlets()
    {
        return $this->hasMany(Atlet::class, 'created_by');
    }

    public function pelatihs()
    {
        return $this->hasMany(Pelatih::class, 'created_by');
    }

    public function lapangans()
    {
        return $this->hasMany(Lapangan::class, 'created_by');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'created_by');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function nagari()
    {
        return $this->belongsTo(Nagari::class, 'nagari_id');
    }

    public function isKecamatan()
    {
        return $this->role == 2;
    }

    public function isNagari()
    {
        return $this->role == 3;
    }

    public function isPelatih()
    {
        return $this->role == 4;
    }

    public function isAtlet()
    {
        return $this->role == 5;
    }

    public function isAdmin()
    {
        return $this->role == 0;
    }

    public function isPegawai()
    {
        return $this->role == 1;
    }
}
