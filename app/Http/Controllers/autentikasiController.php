<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class autentikasiController extends Controller
{
    public function loginAdmin(Request $request){
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:8'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!');
        }

        if(Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])){
            return redirect()->route('dashboard.pegawai');
        }

        return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!');
    }

    public function logoutAdmin(){
        Auth::guard('user')->logout();
        return redirect()->route('auntentikasi');
    }

    public function loginPelatih(){
        return view('autentikasi.loginPelatih');
    }

    public function submitLoginPelatih(Request $request){
        $validate = Validator::make($request->all(),[
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!');
        }

        // First attempt login by email+password
        if (Auth::guard('pelatih')->attempt(['email' => $request->email, 'password' => $request->password])) {
            // Check status after authentication
            $user = Auth::guard('pelatih')->user();
            if (isset($user->status) && strtolower(trim($user->status)) !== 'aktif') {
                Auth::guard('pelatih')->logout();
                return redirect()->back()->with('error', 'Mohon maaf akun anda sudah tidak aktif');
            }

            return redirect()->route('dashboard.pelatih');
        }

        return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!');
    }

    public function logoutPelatih(){
        Auth::guard('pelatih')->logout();
        return redirect()->route('login.pelatih');
    }

    public function loginAtlet(){
        return view('autentikasi.loginAtlet');
    }

    public function submitLoginAtlet(Request $request){
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:255', 
            'password' => 'required|string|min:8',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!');
        }

        // First attempt login by email+password
        if (Auth::guard('atlet')->attempt(['email' => $request->email, 'password' => $request->password])) {
            // Check status after authentication
            $user = Auth::guard('atlet')->user();
            if (isset($user->status) && strtolower(trim($user->status)) !== 'aktif') {
                Auth::guard('atlet')->logout();
                return redirect()->back()->with('error', 'Mohon maaf akun anda sudah tidak aktif');
            }

            return redirect()->route('dashboard.atlet');
        }

        return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!');
    }

    public function logoutAtlet(){
        Auth::guard('atlet')->logout();
        return redirect()->route('login.atlet');
    }
}
