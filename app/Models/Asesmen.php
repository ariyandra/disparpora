<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesmen extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'aspek_fisik' => 'float',
        'aspek_teknik' => 'float',
        'aspek_sikap' => 'float',
    ];

    public function atlet(){
        return $this->belongsTo(Atlet::class, 'id_atlet');
    }

    public function pelatih(){
        return $this->belongsTo(Pelatih::class, 'id_pelatih');
    }
}
