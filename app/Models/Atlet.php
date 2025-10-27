<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cabor;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Atlet extends Authenticatable
{
    use HasFactory;
    protected $guarded = ['id'];
    public function cabor(){
        return $this->belongsTo(Cabor::class, 'id_cabor');
    }

    public function asesmen(){
        return $this->hasMany(Asesmen::class, 'id_atlet');
    }

    public function absensi(){
        return $this->hasMany(Absensi::class, 'id_atlet');
    }

}
