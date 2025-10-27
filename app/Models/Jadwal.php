<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function lapangan(){
        return $this->belongsTo(Lapangan::class, 'id_lapangan');
    }

    public function cabor(){
        return $this->belongsTo(Cabor::class, 'id_cabor');
    }

    
}
