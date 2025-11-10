<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cabor;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Document;

class Atlet extends Authenticatable
{
    use HasFactory;
    protected $guarded = ['id'];
    
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cabor()
    {
        return $this->belongsTo(Cabor::class, 'id_cabor');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function asesmen()
    {
        return $this->hasMany(Asesmen::class, 'id_atlet');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_atlet');
    }

    public function pelatih()
    {
        return $this->belongsTo(Pelatih::class, 'id_pelatih');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function nagari()
    {
        return $this->belongsTo(Nagari::class, 'nagari_id');
    }
}
