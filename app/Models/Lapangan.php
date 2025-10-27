<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function cabor(){
        return $this->belongsTo(Cabor::class, 'id_cabor');
    }
}
