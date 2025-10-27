<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabor extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function pelatih(){
        return $this->hasMany(Pelatih::class, 'id_cabor');
    }

    public function atlet(){
        return $this->hasMany(Atlet::class, 'id_cabor');
    }

    public function lapangan(){
        return $this->hasMany(Lapangan::class, 'id_cabor');
    }

    public function jadwal(){
        return $this->hasMany(Jadwal::class, 'id_cabor');
    }
}
