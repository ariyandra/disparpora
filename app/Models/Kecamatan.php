<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class, 'kecamatan_id');
    }

    public function atlets()
    {
        return $this->hasMany(Atlet::class, 'kecamatan_id');
    }
}
