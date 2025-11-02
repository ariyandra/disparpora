<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nagari extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class, 'nagari_id');
    }

    public function atlets()
    {
        return $this->hasMany(Atlet::class, 'nagari_id');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }
}
