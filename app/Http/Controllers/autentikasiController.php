<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pelatih;
use App\Models\Atlet;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class autentikasiController extends Controller
{
    // ============================
    // Unified Login (untuk semua peran)
    // ============================
    public function unifiedLogin()
    {
        return view('autentikasi.login');
    }

    public function submitUnifiedLogin(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'role' => 'required|in:admin,pelatih,atlet',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ]);
        if($validate->fails()){
            return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!')->withInput();
        }

        $credentials = ['email' => $request->email, 'password' => $request->password];
        if($request->role === 'admin'){
            if(Auth::guard('user')->attempt($credentials)){
                return redirect()->route('dashboard.pegawai');
            }
            return redirect()->back()->with('error', 'Email atau password salah.')->withInput();
        }
        if($request->role === 'pelatih'){
            if(Auth::guard('pelatih')->attempt($credentials)){
                $user = Auth::guard('pelatih')->user();
                // Ensure account is active
                if (isset($user->status) && strtolower(trim($user->status)) !== 'aktif') {
                    Auth::guard('pelatih')->logout();
                    return redirect()->back()->with('error', 'Mohon maaf akun anda sudah tidak aktif')->withInput();
                }
                // Ensure verification is approved
                if (isset($user->status_verifikasi) && strtolower(trim($user->status_verifikasi)) !== 'approved') {
                    Auth::guard('pelatih')->logout();
                    return redirect()->back()->with('error', 'Akun Anda belum diverifikasi. Mohon tunggu sampai verifikasi selesai.')->withInput();
                }
                return redirect()->route('dashboard.pelatih');
            }
            return redirect()->back()->with('error', 'Email atau password salah.')->withInput();
        }
        if($request->role === 'atlet'){
            if(Auth::guard('atlet')->attempt($credentials)){
                $user = Auth::guard('atlet')->user();
                // Ensure account is active
                if (isset($user->status) && strtolower(trim($user->status)) !== 'aktif') {
                    Auth::guard('atlet')->logout();
                    return redirect()->back()->with('error', 'Mohon maaf akun anda sudah tidak aktif')->withInput();
                }
                // Ensure verification is approved
                if (isset($user->status_verifikasi) && strtolower(trim($user->status_verifikasi)) !== 'approved') {
                    Auth::guard('atlet')->logout();
                    return redirect()->back()->with('error', 'Akun Anda belum diverifikasi. Mohon tunggu sampai verifikasi selesai.')->withInput();
                }
                return redirect()->route('dashboard.atlet');
            }
            return redirect()->back()->with('error', 'Email atau password salah.')->withInput();
        }

        return redirect()->back()->with('error', 'Peran tidak valid.');
    }
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
        return redirect()->route('login');
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

            // Block login if verification not yet approved
            if (isset($user->status_verifikasi) && strtolower(trim($user->status_verifikasi)) !== 'approved') {
                Auth::guard('pelatih')->logout();
                return redirect()->back()->with('error', 'Akun Anda belum diverifikasi. Mohon tunggu sampai verifikasi selesai.');
            }

            return redirect()->route('dashboard.pelatih');
        }

        return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!');
    }

    public function logoutPelatih(){
        Auth::guard('pelatih')->logout();
        return redirect()->route('login');
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

            // Block login if verification not yet approved
            if (isset($user->status_verifikasi) && strtolower(trim($user->status_verifikasi)) !== 'approved') {
                Auth::guard('atlet')->logout();
                return redirect()->back()->with('error', 'Akun Anda belum diverifikasi. Mohon tunggu sampai verifikasi selesai.');
            }

            return redirect()->route('dashboard.atlet');
        }

        return redirect()->back()->with('error', 'Periksa kembali data yang anda masukkan!');
    }

    public function logoutAtlet(){
        Auth::guard('atlet')->logout();
        return redirect()->route('login');
    }

    // ============================
    // Forgot Password (Option B)
    // ============================
    public function forgotPasswordForm()
    {
        return view('autentikasi.password_request');
    }

    public function forgotPasswordSubmit(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'role' => 'required|in:admin,pelatih,atlet',
            'email' => 'required|email',
        ]);
        if($validate->fails()){
            return redirect()->back()->withErrors($validate)->withInput();
        }
        $data = $validate->validated();
        // Always generate a reset token and attempt to store/send it.
        // This ensures we send a reset email to the provided address regardless of whether it exists in the DB,
        // which prevents account enumeration and matches the requested behavior.
        $tokenPlain = Str::random(64);
        $tokenHash = hash('sha256', $tokenPlain);
        $expires = now()->addMinutes(30);
        DB::table('password_reset_tokens_custom')->updateOrInsert(
            ['email'=>$data['email'],'role'=>$data['role']],
            ['token_hash'=>$tokenHash,'expires_at'=>$expires,'created_at'=>now()]
        );

        try{
            $resetUrl = url('/password/reset/'.$tokenPlain.'?email='.urlencode($data['email']).'&role='.$data['role']);
            Mail::send('emails.password_reset', ['resetUrl'=>$resetUrl,'role'=>$data['role']], function($m) use ($data){
                $m->to($data['email'])->subject('Reset Password Akun');
            });
        }catch(\Exception $e){
            // If sending failed, return an error so the user can retry (likely SMTP/config issue)
            return redirect()->back()->with('error','Gagal mengirim email reset. Periksa konfigurasi email.')->withInput();
        }

        // Inform the requester that a reset link was sent to the provided address. We do this regardless
        // of whether an account exists to avoid leaking which emails are registered.
        return redirect()->back()->with('success','Tautan reset telah dikirim ke alamat email yang Anda masukkan. Jika Anda menerima email tersebut, ikuti instruksi untuk mereset password.');
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        $role = $request->query('role');
        return view('autentikasi.password_reset', compact('token','email','role'));
    }

    public function submitPasswordReset(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'role' => 'required|in:admin,pelatih,atlet',
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if($validate->fails()){
            return redirect()->back()->withErrors($validate)->withInput();
        }
        $data = $validate->validated();

        $row = DB::table('password_reset_tokens_custom')
            ->where('email',$data['email'])
            ->where('role',$data['role'])
            ->first();
        if(!$row){
            return redirect()->back()->with('error','Token tidak valid atau telah digunakan.')->withInput();
        }
        if(now()->greaterThan($row->expires_at)){
            return redirect()->back()->with('error','Token kedaluwarsa.')->withInput();
        }
        $check = hash_equals($row->token_hash, hash('sha256', $data['token']));
        if(!$check){
            return redirect()->back()->with('error','Token tidak valid.')->withInput();
        }

        if ($data['role'] === 'admin') {
            $user = User::where('email', $data['email'])->first();
            if($user){ $user->password = bcrypt($data['password']); $user->save(); }
        }
        if ($data['role'] === 'pelatih') {
            $pelatih = Pelatih::where('email', $data['email'])->first();
            if($pelatih){ $pelatih->password = bcrypt($data['password']); $pelatih->save(); }
        }
        if ($data['role'] === 'atlet') {
            $atlet = Atlet::where('email', $data['email'])->first();
            if($atlet){ $atlet->password = bcrypt($data['password']); $atlet->save(); }
        }

        DB::table('password_reset_tokens_custom')->where('email',$data['email'])->where('role',$data['role'])->delete();
        return redirect()->route('login')->with('success','Password berhasil diperbarui. Silakan masuk.');
    }
}
