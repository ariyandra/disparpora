<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function atlet()
    {
        return $this->belongsTo(Atlet::class, 'id_atlet');
    }
}
