<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pelatih extends Authenticatable
{
    use HasFactory;
    protected $guarded = ['id'];
    
    public function cabor(){
        return $this->belongsTo(Cabor::class, 'id_cabor');
    }

    public function asesmen(){
        return $this->hasMany(Asesmen::class, 'id_pelatih');
    }
}
