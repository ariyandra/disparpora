<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Atlet;
use App\Models\Pelatih;
use App\Models\Lapangan;
use App\Models\Cabor;

class Controller extends BaseController
{
    public function index(){
        $jml_atlit = Atlet::count();
        $jml_pelatih = Pelatih::count();
        $jml_lapangan = Lapangan::count();
        $jml_cabor = Cabor::count();
        return view('dashboard', compact('jml_atlit', 'jml_pelatih', 'jml_lapangan', 'jml_cabor'));
    }

    public function Autentikasi(){
        return view('autentikasi.login');
    }

    
}
