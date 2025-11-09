<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Asesmen;
use App\Models\Atlet;
use App\Models\Cabor;
use App\Models\Jadwal;
use App\Models\Lapangan;
use App\Models\Notif;
use App\Models\Pelatih;
use App\Models\User;
use App\Models\Kecamatan;
use App\Models\Nagari;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AtletImport;
use App\Imports\PelatihImport;

class adminController extends Controller
{
    public function index(){
        $user = auth()->user();
        
        // Query builders
        $atletQuery = Atlet::query();
        $pelatihQuery = Pelatih::query();
        $lapanganQuery = Lapangan::query();
        $caborQuery = Cabor::query();

        $jml_atlit = $atletQuery->count();
        $jml_pelatih = $pelatihQuery->count();
        $jml_lapangan = $lapanganQuery->count();
        $jml_cabor = $caborQuery->count();
        $notif = Notif::where('is_read', false)->take(5)->get();
        
        return view('pegawai.dashboard', compact('jml_atlit', 'jml_pelatih', 'jml_lapangan', 'jml_cabor', 'notif'));
    }

    // Verifikasi: daftar dan aksi
    public function verifikasi(){
        $user = auth()->user();
        if (!in_array($user->role, [0,1,2])){
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman verifikasi.');
        }
        $targetStatus = ($user->role == 2) ? 'pending_kecamatan' : 'pending_admin';
        $atlets = Atlet::where('status_verifikasi', $targetStatus)->get();
        $pelatihs = Pelatih::where('status_verifikasi', $targetStatus)->get();
        $cabors = Cabor::where('status_verifikasi', $targetStatus)->get();
        $lapangans = Lapangan::where('status_verifikasi', $targetStatus)->get();
        $jadwals = Jadwal::where('status_verifikasi', $targetStatus)->get();
        return view('pegawai.verifikasi', compact('atlets','pelatihs','cabors','lapangans','jadwals','targetStatus'));
    }

    public function verifikasiApprove(Request $request){
        $validate = Validator::make($request->all(), [
            'entity' => 'required|in:atlets,pelatihs,cabors,lapangans,jadwals',
            'id' => 'required|integer'
        ]);
        if($validate->fails()) return redirect()->back()->with('error','Data tidak valid');
        $user = auth()->user();
        $entity = $request->entity; $id = $request->id;
        $modelMap = [
            'atlets' => Atlet::class,
            'pelatihs' => Pelatih::class,
            'cabors' => Cabor::class,
            'lapangans' => Lapangan::class,
            'jadwals' => Jadwal::class,
        ];
        $model = $modelMap[$entity]::find($id);
        if(!$model) return redirect()->back()->with('error','Data tidak ditemukan');
        // If Kecamatan approves pending_kecamatan, escalate to pending_admin; if Admin approves pending_admin -> approved
        if ($user->role == 2 && $model->status_verifikasi === 'pending_kecamatan'){
            $model->status_verifikasi = 'pending_admin';
        } elseif (in_array($user->role, [0,1]) && $model->status_verifikasi === 'pending_admin'){
            $model->status_verifikasi = 'approved';
            $model->verified_by = $user->id;
            $model->verified_at = now();
        } else {
            return redirect()->back()->with('error','Status tidak sesuai untuk disetujui');
        }
        $model->rejection_reason = null;
        $model->save();
        return redirect()->back()->with('success','Data berhasil diverifikasi');
    }

    public function verifikasiReject(Request $request){
        $validate = Validator::make($request->all(), [
            'entity' => 'required|in:atlets,pelatihs,cabors,lapangans,jadwals',
            'id' => 'required|integer',
            'reason' => 'nullable|string'
        ]);
        if($validate->fails()) return redirect()->back()->with('error','Data tidak valid');
        $user = auth()->user();
        if (!in_array($user->role, [0,1,2])) return redirect()->back()->with('error','Tidak berwenang');
        $entity = $request->entity; $id=$request->id; $reason=$request->reason;
        $modelMap = [
            'atlets' => Atlet::class,
            'pelatihs' => Pelatih::class,
            'cabors' => Cabor::class,
            'lapangans' => Lapangan::class,
            'jadwals' => Jadwal::class,
        ];
        $model = $modelMap[$entity]::find($id);
        if(!$model) return redirect()->back()->with('error','Data tidak ditemukan');
        $model->status_verifikasi = 'rejected';
        $model->rejection_reason = $reason;
        $model->save();
        return redirect()->back()->with('success','Data berhasil ditolak');
    }

    public function pagePelatih(){
        $user = auth()->user();
        $query = Pelatih::with('documents');

        // Admin/Pegawai (0/1) lihat semua; Kecamatan/Nagari (2/3) hanya yang dibuat sendiri
        if ($user->role == 0 || $user->role == 1) {
            $dataPelatih = $query->get();
        } elseif ($user->role == 2) { // Kecamatan: lihat buatan sendiri + pending_kecamatan
            $dataPelatih = $query->where(function($q) use ($user){
                $q->where('created_by', $user->id)
                  ->orWhere('status_verifikasi', 'pending_kecamatan');
            })->get();
        } elseif ($user->role == 3) { // Nagari: hanya buatan sendiri
            $dataPelatih = $query->where('created_by', $user->id)->get();
        } else {
            $dataPelatih = $query->where('status_verifikasi', 'approved')->get();
        }

        return view('pegawai.pelatih', compact('dataPelatih'));
    }

    public function tambahPelatih(){
        $dataCabor = Cabor::all();
        return view('insert.pelatih', compact('dataCabor'));
    }

    public function simpanPelatihBaru(Request $request){
        $validate = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'required|string|max:10',
            'no_hp' => 'required|string|max:15',
            'cabor' => 'required|exists:cabors,id',
            'tanggal_lahir' => 'required|date',
            'tanggal_gabung' => 'required|date',
            'status' => 'required|string|max:20'
        ]);

        if($validate->fails()){
            return redirect()->back()->withErrors($validate)->withInput();
        }
        
        $emailExists = Pelatih::where('email', $request->email)->exists();
        if ($emailExists) {
            return redirect()->back()->with('error', 'Email sudah terdaftar.');
        }

        $data = $validate->validated();

        $user = auth()->user();
        $pelatih = new Pelatih();
        $pelatih->nama = $data['nama'];
        $pelatih->email = $data['email'];
        $pelatih->password = bcrypt($data['password']);
        $pelatih->jenis_kelamin = $data['jenis_kelamin'];
        $pelatih->no_telp = $data['no_hp'];
        $pelatih->id_cabor = $data['cabor'];
        $pelatih->tanggal_lahir = $data['tanggal_lahir'];
        $pelatih->tanggal_gabung = $data['tanggal_gabung'];
        $pelatih->status = $data['status'];
        $pelatih->created_by = $user->id;
        // verification pipeline
        if ($user->role == 3) { // Nagari
            $pelatih->status_verifikasi = 'pending_kecamatan';
        } elseif ($user->role == 2) { // Kecamatan
            $pelatih->status_verifikasi = 'pending_admin';
        } else { // Admin/Pegawai
            $pelatih->status_verifikasi = 'approved';
            $pelatih->verified_by = $user->id;
            $pelatih->verified_at = now();
        }

        // Set kecamatan_id and nagari_id if the user is from kecamatan or nagari
        if ($user->role == 2) { // Kecamatan
            $pelatih->kecamatan_id = $user->kecamatan_id;
        } elseif ($user->role == 3) { // Nagari
            $pelatih->nagari_id = $user->nagari_id;
        }

        if($request->hasFile('foto')){
            $path = $request->file('foto')->store('pelatih', 'public');
            $pelatih->foto = $path;
        }

        $simpan =$pelatih->save();
        
        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menambahkan data pelatih baru: ' . $data['nama'];
        $notif->kategori = 'anggota';
        $notif->save();
        return redirect()->route('data.pelatih')->with('success', 'Data pelatih berhasil disimpan.');
    }

    public function hapusPelatih(Request $request){
        $validate = Validator::make($request->all(), [
            'id_pelatih' => 'required|exists:pelatihs,id',
        ]);
        $pelatih = Pelatih::findOrFail($request->id_pelatih);
        $hapus = $pelatih->delete();
        if(!$hapus){
            return redirect()->back()->with('error', 'Gagal menghapus data pelatih.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menghapus data pelatih: ' . $pelatih->nama;
        $notif->kategori = 'anggota';
        $notif->save();
        return redirect()->route('data.pelatih')->with('success', 'Data pelatih berhasil dihapus.');
    }

    public function updatePelatih(Request $request){
        if(!$request->id_pelatih){
            return redirect()->back()->with('error', 'ID pelatih tidak ditemukan.');
        }
        $dataPelatih = Pelatih::findOrFail($request->id_pelatih);
        $dataCabor = Cabor::all();
        return view('insert.updatePelatih', compact('dataPelatih', 'dataCabor'));
    }

    public function simpanUpdatePelatih(Request $request){
        
        $validate = Validator::make($request->all(), [
            'id_pelatih' => 'required|exists:pelatihs,id',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'jenis_kelamin' => 'required|string|max:10',
            'no_hp' => 'required|string|max:15',
            'cabor' => 'required|exists:cabors,id',
            'tanggal_lahir' => 'required|date',
            'tanggal_gabung' => 'required|date',
            'status' => 'required|string|max:20'
        ]);
        
        if($validate->fails()){
            return redirect()->route('data.pelatih')->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $data = $validate->validated();
        $emailExist = Pelatih::where('email', $data['email'])
                    ->where('id', '!=', $data['id_pelatih'])
                    ->exists();

        if($emailExist){
            return redirect()->route('data.pelatih')->with('error', 'Email sudah terdaftar, gunakan email lain');
        }
        
        $pelatih = Pelatih::findOrFail($data['id_pelatih']);
        $pelatih->nama = $data['nama'];
        $pelatih->email = $data['email'];
        $pelatih->password = Pelatih::findOrFail($data['id_pelatih'])->password;
        $pelatih->jenis_kelamin = $data['jenis_kelamin'];
        $pelatih->no_telp = $data['no_hp'];
        $pelatih->id_cabor = $data['cabor'];
        $pelatih->tanggal_lahir = $data['tanggal_lahir'];
        $pelatih->tanggal_gabung = $data['tanggal_gabung'];
        $pelatih->status = $data['status'];

        if($request->hasFile('foto')){
            // delete old foto if exists
            if(!empty($pelatih->foto) && Storage::disk('public')->exists($pelatih->foto)){
                Storage::disk('public')->delete($pelatih->foto);
            }
            $path = $request->file('foto')->store('pelatih', 'public');
            $pelatih->foto = $path;
        }

        $simpan = $pelatih->save();

        if(!$simpan){
            return redirect()->route('update.pelatih')->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') mengubah data pelatih: ' . $data['nama'];
        $notif->kategori = 'anggota';
        $notif->save();
        return redirect()->route('data.pelatih')->with('success', 'Data pelatih berhasil diperbarui.');
    }

    public function dataAtlet(){
        $user = auth()->user();
        $query = Atlet::with('documents');
        if ($user->role == 0 || $user->role == 1) {
            $dataAtlet = $query->get();
        } elseif ($user->role == 2) { // Kecamatan
            $dataAtlet = $query->where(function($q) use ($user){
                $q->where('created_by', $user->id)
                  ->orWhere('status_verifikasi', 'pending_kecamatan');
            })->get();
        } elseif ($user->role == 3) { // Nagari
            $dataAtlet = $query->where('created_by', $user->id)->get();
        } else {
            $dataAtlet = $query->where('status_verifikasi', 'approved')->get();
        }

        return view('pegawai.atlet', compact('dataAtlet'));
    }

    public function tambahAtlet(){
        $dataCabor = Cabor::all();
        return view('insert.atlet', compact('dataCabor'));
    }

    public function simpanAtletBaru(Request $request){
        $validate = Validator::make($request->all(), [
            'nama' => 'required|string|max:255', 
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'required|string|max:10',
            'no_hp' => 'required|string|max:15',
            'cabor' => 'required|exists:cabors,id',
            'tanggal_lahir' => 'required|date',
            'tanggal_gabung' => 'required|date',
            'status' => 'required|string|max:20'
        ]);

        

        if($validate->fails()){
            
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $emailExists = Atlet::where('email', $request->email)->exists();
        if($emailExists){
            return redirect()->back()->with('error', 'Email sudah terdaftar, gunakan email lain');
        }

        $data = $validate->validated();
        $user = auth()->user();
        $atlet = new Atlet();

        $atlet->nama = $data['nama'];
        $atlet->email = $data['email'];
        $atlet->password = bcrypt($data['password']);
        $atlet->jenis_kelamin = $data['jenis_kelamin'];
        $atlet->no_telp = $data['no_hp'];
        $atlet->id_cabor = $data['cabor'];
        $atlet->tanggal_lahir = $data['tanggal_lahir'];
        $atlet->tanggal_gabung = $data['tanggal_gabung'];
        $atlet->status = $data['status'];
        $atlet->created_by = $user->id;
        if ($user->role == 3) {
            $atlet->status_verifikasi = 'pending_kecamatan';
        } elseif ($user->role == 2) {
            $atlet->status_verifikasi = 'pending_admin';
        } else {
            $atlet->status_verifikasi = 'approved';
            $atlet->verified_by = $user->id;
            $atlet->verified_at = now();
        }
        // handle foto upload
        if($request->hasFile('foto')){
            $path = $request->file('foto')->store('atlets', 'public');
            $atlet->foto = $path;
        }

        $simpan = $atlet->save();

        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menambahkan data atlet baru: ' . $data['nama'];
        $notif->kategori = 'anggota';
        $notif->save();
        return redirect()->route('data.atlet')->with('success', 'Data atlet berhasil disimpan.');
    }

    public function hapusAtlet(Request $request){
        $validate = Validator::make($request->all(), [
            'id_atlet' => 'required|exists:atlets,id'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $atlet = Atlet::findOrFail($request->id_atlet);
        $hapus = $atlet->delete();

        if(!$hapus){
            return redirect()->route('data.atlet')->with('error', 'Gagal menghapus data atlet.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menghapus data atlet: ' . $atlet->nama;
        $notif->kategori = 'anggota';
        $notif->save();
        return redirect()->route('data.atlet')->with('success', 'Data atlet berhasil dihapus.');
    }

    public function updateAtlet(Request $request){
        $validate = Validator::make($request->all(), [
            'id_atlet' => 'required|exists:atlets,id'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $atlet = Atlet::findOrFail($request->id_atlet);
        $dataCabor = Cabor::all();
        return view('insert.updateAtlet', compact('atlet', 'dataCabor'));
    }

    public function simpanUpdateAtlet(Request $request){
        $validate = Validator::make($request->all(), [
            'id_atlet' => 'required|exists:atlets,id',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:10',
            'password' => 'nullable|string|min:8',
            'no_hp' => 'required|string|max:15',
            'cabor' => 'required|exists:cabors,id',
            'tanggal_lahir' => 'required|date',
            'tanggal_gabung' => 'required|date',
            'status' => 'required|string|max:20'
        ]); 

        //jika email yang diubah sudah digunakan oleh pengguna lain, berikan pesan error
        $emailExist = Atlet::where('email', $request->email)
                    ->where('id', '!=', $request->id_atlet)
                    ->exists();

        if($emailExist){
            return redirect()->route('data.atlet')->with('error', 'Email sudah terdaftar, gunakan email lain');
        }

        $data = $validate->validated();
        $atlet = Atlet::findOrFail($data['id_atlet']);

        $atlet->nama = $data['nama'];
        $atlet->email = $data['email'];
        // only update password if provided
        if(isset($data['password']) && $data['password']){
            $atlet->password = bcrypt($data['password']);
        }
        $atlet->jenis_kelamin = $data['jenis_kelamin'];
        $atlet->no_telp = $data['no_hp'];
        $atlet->id_cabor = $data['cabor'];
        $atlet->tanggal_lahir = $data['tanggal_lahir'];
        $atlet->tanggal_gabung = $data['tanggal_gabung'];
        $atlet->status = $data['status'];

        // handle foto upload (replace existing)
        if($request->hasFile('foto')){
            // delete old foto if exists
            if(!empty($atlet->foto) && Storage::disk('public')->exists($atlet->foto)){
                Storage::disk('public')->delete($atlet->foto);
            }
            $path = $request->file('foto')->store('atlets', 'public');
            $atlet->foto = $path;
        }

        $simpan = $atlet->save();

        if(!$simpan){
            return redirect()->route('data.atlet')->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') mengubah data atlet: ' . $data['nama'];
        $notif->kategori = 'anggota';
        $notif->save();
        return redirect()->route('data.atlet')->with('success', 'Data atlet berhasil disimpan.');
    }

    public function cabor(){
        $user = auth()->user();
        $query = Cabor::query();
        if ($user->role == 0 || $user->role == 1) {
            $dataCabor = $query->get();
        } elseif ($user->role == 2) {
            $dataCabor = $query->where(function($q) use ($user){
                $q->where('created_by', $user->id)
                  ->orWhere('status_verifikasi', 'pending_kecamatan');
            })->get();
        } elseif ($user->role == 3) {
            $dataCabor = $query->where('created_by', $user->id)->get();
        } else {
            $dataCabor = $query->where('status_verifikasi', 'approved')->get();
        }

        return view('pegawai.cabor', compact('dataCabor'));
    }

    public function caborBaru(){
        return view('insert.cabor');
    }

    public function simpanCaborBaru(Request $request){
        $validate = Validator::make($request->all(), [
            'nama_cabor' => 'required|string|max:255',
            'deskripsi' =>'required|string',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $data = $validate->validated();
        $user = auth()->user();
        $cabor = new Cabor();
        $cabor->nama_cabor = $data['nama_cabor'];
        $cabor->deskripsi = $data['deskripsi'];
        $cabor->created_by = $user->id;
        if ($user->role == 3) {
            $cabor->status_verifikasi = 'pending_kecamatan';
        } elseif ($user->role == 2) {
            $cabor->status_verifikasi = 'pending_admin';
        } else {
            $cabor->status_verifikasi = 'approved';
            $cabor->verified_by = $user->id;
            $cabor->verified_at = now();
        }
        
        // Set kecamatan_id and nagari_id if the user is from kecamatan or nagari
        if ($user->role == 2) { // Kecamatan
            $cabor->kecamatan_id = $user->kecamatan_id;
        } elseif ($user->role == 3) { // Nagari
            $cabor->nagari_id = $user->nagari_id;
        }
        
        $simpan = $cabor->save();

        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menambahkan data cabang olahraga baru: ' . $data['nama_cabor'];
        $notif->kategori = 'cabor';
        $notif->save();
        return redirect()->route('cabor')->with('success', 'Data cabang olahraga berhasil disimpan.');
    }

    public function hapusCabor(Request $request){
        $validate = Validator::make($request->all(), [
            'id_cabor' => 'required|exists:cabors,id'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $cabor = Cabor::findOrFail($request->id_cabor);
        $simpan = $cabor->delete();

        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menghapus data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menghapus data cabang olahraga: ' . $cabor->nama_cabor;
        $notif->kategori = 'cabor';
        $notif->save();
        return redirect()->route('cabor')->with('success', 'Data cabang olahraga berhasil dihapus.');
    }

    public function ubahCabor(Request $request){
        $validate = Validator::make($request->all(), [
            'id_cabor' => 'required|exists:cabors,id'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        $dataCabor = Cabor::findOrFail($request->id_cabor);
        return view('insert.updateCabor', compact('dataCabor'));
    }

    public function simpanUbahCabor(Request $request){
        $validate = Validator::make($request->all(), [
            'id_cabor' => 'required|exists:cabors,id',
            'nama_cabor' => 'required|string|max:255',
            'deskripsi' =>'required|string',
        ]);

        if($validate->fails()){
            return redirect()->route('cabor')->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $data = $validate->validated();
        $cabor = Cabor::findOrFail($data['id_cabor']);
        $cabor->nama_cabor = $data['nama_cabor'];
        $cabor->deskripsi = $data['deskripsi'];
        $simpan = $cabor->save();

        if(!$simpan){
            return redirect()->route('cabor')->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') mengubah data cabang olahraga: ' . $data['nama_cabor'];
        $notif->kategori = 'cabor';
        $notif->save();
        return redirect()->route('cabor')->with('success', 'Data cabang olahraga berhasil diubah.');
    }

    public function lapangan(){
        $user = auth()->user();
        $query = Lapangan::query();
        if ($user->role == 0 || $user->role == 1) {
            $dataLapangan = $query->get();
        } elseif ($user->role == 2) {
            $dataLapangan = $query->where(function($q) use ($user){
                $q->where('created_by', $user->id)
                  ->orWhere('status_verifikasi', 'pending_kecamatan');
            })->get();
        } elseif ($user->role == 3) {
            $dataLapangan = $query->where('created_by', $user->id)->get();
        } else {
            $dataLapangan = $query->where('status_verifikasi', 'approved')->get();
        }

        return view('pegawai.lapangan', compact('dataLapangan'));
    }

    public function lapanganBaru(){
        $dataCabor = Cabor::all();
        return view('insert.lapangan', compact('dataCabor'));
    }

    public function simpanLapanganBaru(Request $request){
        $validate = Validator::make($request->all(),[
            'nama_lapangan' => 'required|string|max:255',
            'cabor' => 'required|exists:cabors,id',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $data = $validate->validated();
        $lapangan = new Lapangan();
        $lapangan->nama_lapangan = $data['nama_lapangan'];
        $lapangan->id_cabor = $data['cabor'];
        $lapangan->lokasi = $data['lokasi'];
        $lapangan->deskripsi = $data['deskripsi'];
        // verification attrs
        $user = auth()->user();
        $lapangan->created_by = $user->id;
        if ($user->role == 3) {
            $lapangan->status_verifikasi = 'pending_kecamatan';
        } elseif ($user->role == 2) {
            $lapangan->status_verifikasi = 'pending_admin';
        } else {
            $lapangan->status_verifikasi = 'approved';
            $lapangan->verified_by = $user->id;
            $lapangan->verified_at = now();
        }

        $simpan = $lapangan->save();

        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }


        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menambahkan data lapangan baru: ' . $data['nama_lapangan'];
        $notif->kategori = 'lapangan';
        $notif->save();
        return redirect()->route('lapangan')->with('success', 'Data lapangan berhasil disimpan.');
    }

    public function updateDataLapangan(Request $request){
        $validate = Validator::make($request->all(), [
            'id_lapangan' => 'required|exists:lapangans,id'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        $dataLapangan = Lapangan::findOrFail($request->id_lapangan); 
        $dataCabor = Cabor::all();
        return view('insert.updateLapangan', compact('dataLapangan', 'dataCabor'));
    }

    public function simpanUbahLapangan(Request $request){
        $validate = Validator::make($request->all(),[
            'id_lapangan' => 'required|exists:lapangans,id',
            'nama_lapangan' => 'required|string|max:255',
            'cabor' => 'required|exists:cabors,id',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        if($validate->fails()){
            return redirect()->route('lapangan')->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $data = $validate->validated();
        $lapangan = Lapangan::findOrFail($data['id_lapangan']);
        $lapangan->nama_lapangan = $data['nama_lapangan'];
        $lapangan->id_cabor = $data['cabor'];
        $lapangan->lokasi = $data['lokasi'];
        $lapangan->deskripsi = $data['deskripsi'];
        $simpan = $lapangan->save();

        if(!$simpan){
            return redirect()->route('lapangan')->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menambahkan data lapangan baru: ' . $data['nama_lapangan'];
        $notif->kategori = 'lapangan';
        $notif->save();

        return redirect()->route('lapangan')->with('success', 'Data lapangan berhasil diubah.');
    }

    public function hapusLapangan(Request $request){
        $validate = Validator::make($request->all(), [
            'id_lapangan' => 'required|exists:lapangans,id'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $lapangan = Lapangan::findOrFail($request->id_lapangan);
        $simpan = $lapangan->delete();

        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menghapus data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menghapus data lapangan: ' . $lapangan->nama_lapangan;
        $notif->kategori = 'lapangan';
        $notif->save();
        return redirect()->back()->with('success', 'Data lapangan berhasil dihapus.');
    }

    public function asesmen(){
        $dataAsesmen = Asesmen::all();
        return view('pegawai.asesmen', compact('dataAsesmen'));
    }

    public function absensi(){
        $dataAbsensi = Absensi::all();
        return view('pegawai.absensi', compact('dataAbsensi'));
    }

    public function jadwal(){
        $user = auth()->user();
        $query = Jadwal::query();
        if ($user->role == 0 || $user->role == 1) {
            $dataJadwal = $query->get();
        } elseif ($user->role == 2) {
            $dataJadwal = $query->where(function($q) use ($user){
                $q->where('created_by', $user->id)
                  ->orWhere('status_verifikasi', 'pending_kecamatan');
            })->get();
        } elseif ($user->role == 3) {
            $dataJadwal = $query->where('created_by', $user->id)->get();
        } else {
            $dataJadwal = $query->where('status_verifikasi', 'approved')->get();
        }

        return view('pegawai.jadwal', compact('dataJadwal'));
    }

    public function jadwalBaru(){
        $dataCabor = Cabor::all();
        $dataLapangan = Lapangan::all();
        return view('insert.jadwal', compact('dataCabor', 'dataLapangan'));
    }

    // ======================
    // Documents (Atlet/Pelatih)
    // ======================
    public function uploadDocumentsAtlet(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'atlet_id' => 'required|exists:atlets,id',
            'kategori' => 'nullable|string|max:50',
            'files' => 'required',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png,webp|max:5120', // 5MB each
        ]);
        if($validate->fails()){
            return redirect()->back()->with('error', 'File tidak valid. Pastikan format pdf/jpg/jpeg/png/webp dan ukuran <= 5MB.');
        }
        $user = auth()->user();
        $atlet = Atlet::findOrFail($request->atlet_id);
        foreach ($request->file('files') as $file){
            $path = $file->store('documents/atlets/'.$atlet->id, 'public');
            Document::create([
                'documentable_type' => Atlet::class,
                'documentable_id' => $atlet->id,
                'kategori' => $request->kategori,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $user ? $user->id : null,
            ]);
        }
        return redirect()->back()->with('success', 'Dokumen atlet berhasil diunggah.');
    }

    public function deleteDocumentAtlet(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'document_id' => 'required|exists:documents,id',
        ]);
        if($validate->fails()){
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }
        $doc = Document::findOrFail($request->document_id);
        if(!empty($doc->path) && Storage::disk('public')->exists($doc->path)){
            Storage::disk('public')->delete($doc->path);
        }
        $doc->delete();
        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function uploadDocumentsPelatih(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'pelatih_id' => 'required|exists:pelatihs,id',
            'kategori' => 'nullable|string|max:50',
            'files' => 'required',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);
        if($validate->fails()){
            return redirect()->back()->with('error', 'File tidak valid. Pastikan format pdf/jpg/jpeg/png/webp dan ukuran <= 5MB.');
        }
        $user = auth()->user();
        $pelatih = Pelatih::findOrFail($request->pelatih_id);
        foreach ($request->file('files') as $file){
            $path = $file->store('documents/pelatihs/'.$pelatih->id, 'public');
            Document::create([
                'documentable_type' => Pelatih::class,
                'documentable_id' => $pelatih->id,
                'kategori' => $request->kategori,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $user ? $user->id : null,
            ]);
        }
        return redirect()->back()->with('success', 'Dokumen pelatih berhasil diunggah.');
    }

    public function deleteDocumentPelatih(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'document_id' => 'required|exists:documents,id',
        ]);
        if($validate->fails()){
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }
        $doc = Document::findOrFail($request->document_id);
        if(!empty($doc->path) && Storage::disk('public')->exists($doc->path)){
            Storage::disk('public')->delete($doc->path);
        }
        $doc->delete();
        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    // Stream document inline (PDF/images) with correct headers
    public function viewDocument($id)
    {
        $doc = Document::findOrFail($id);
        $disk = Storage::disk('public');
        if(!$doc->path || !$disk->exists($doc->path)){
            abort(404);
        }
        $fullPath = storage_path('app/public/'.$doc->path);
        $mime = $doc->mime ?: mime_content_type($fullPath);
        $headers = [
            'Content-Type' => $mime,
            // display inline in browser if possible (e.g., PDFs)
            'Content-Disposition' => 'inline; filename="'.basename($doc->original_name).'"'
        ];
        return response()->file($fullPath, $headers);
    }

    public function simpanJadwal(Request $request){
        
        $validate = Validator::make($request->all(), [
            'cabang_olahraga' => 'required|exists:cabors,id',
            'lapangan' => 'required|exists:lapangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'keterangan' => 'nullable|string'
        ]);

        
        if($validate->fails()){
            
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $data = $validate->validated();
        $jadwal = new Jadwal();
        $jadwal->id_cabor = $data['cabang_olahraga'];
        $jadwal->id_lapangan = $data['lapangan'];
        $jadwal->tanggal = $data['tanggal'];
        $jadwal->jam_mulai = $data['jam_mulai'];
        $jadwal->jam_selesai = $data['jam_selesai'];
        $jadwal->keterangan = $data['keterangan'] ?? '';
        $user = auth()->user();
        $jadwal->created_by = $user->id;
        if ($user->role == 3) {
            $jadwal->status_verifikasi = 'pending_kecamatan';
        } elseif ($user->role == 2) {
            $jadwal->status_verifikasi = 'pending_admin';
        } else {
            $jadwal->status_verifikasi = 'approved';
            $jadwal->verified_by = $user->id;
            $jadwal->verified_at = now();
        }
        $simpan = $jadwal->save();

        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menambahkan data jadwal baru: ' . $data['tanggal'];
        $notif->kategori = 'jadwal';
        $notif->save();

        // jika notif kategori jadwal, kirim email ke semua pengguna (User, Pelatih, Atlet)
        if($notif->kategori === 'jadwal'){
            try{
                $emails = [];
                $emails = array_merge($emails, User::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_merge($emails, Pelatih::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_merge($emails, Atlet::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_filter(array_unique($emails));
                if(!empty($emails)){
                    $dataMail = ['keterangan' => $notif->keterangan];
                    Mail::send('emails.jadwal_notification', $dataMail, function($message) use ($emails){
                        $message->to($emails);
                        $message->subject('Pemberitahuan Jadwal Baru');
                    });
                }
            } catch(\Exception $e){
                Log::error('Gagal mengirim email notifikasi jadwal: ' . $e->getMessage());
            }
        }

        return redirect()->route('jadwal')->with('success', 'Data jadwal berhasil disimpan.');
    }

    public function ubahJadwal(Request $request){
        $validate = Validator::make($request->all(), [
            'id_jadwal' => 'required|exists:jadwals,id'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $data = $validate->validated();
        $jadwal = Jadwal::findOrFail($request->id_jadwal);
        $dataCabor = Cabor::all();
        $dataLapangan = Lapangan::all();
        return view('insert.ubahJadwal', compact('jadwal', 'dataCabor', 'dataLapangan'));
    }

    public function simpanUbahJadwal(Request $request){
        $validate = Validator::make($request->all(), [
            'id_jadwal' => 'required|exists:jadwals,id',
            'cabang_olahraga' => 'required|exists:cabors,id',
            'lapangan' => 'required|exists:lapangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'keterangan' => 'nullable|string'
        ]);

        if($validate->fails()){
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $data = $validate->validated();
        $jadwal = Jadwal::findOrFail($data['id_jadwal']);
        $jadwal->id_cabor = $data['cabang_olahraga'];
        $jadwal->id_lapangan = $data['lapangan'];
        $jadwal->tanggal = $data['tanggal'];
        $jadwal->jam_mulai = $data['jam_mulai'];
        $jadwal->jam_selesai = $data['jam_selesai'];
        $jadwal->keterangan = $data['keterangan'] ?? '';
        $simpan = $jadwal->save();

        if(!$simpan){
            return redirect()->route('jadwal')->with('error', 'Gagal mengubah data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') mengubah data jadwal tanggal: ' . $data['tanggal'];
        $notif->kategori = 'jadwal';
        $notif->save();
        if($notif->kategori === 'jadwal'){
            try{
                $emails = [];
                $emails = array_merge($emails, User::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_merge($emails, Pelatih::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_merge($emails, Atlet::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_filter(array_unique($emails));
                if(!empty($emails)){
                    $dataMail = ['keterangan' => $notif->keterangan];
                    Mail::send('emails.jadwal_notification', $dataMail, function($message) use ($emails){
                        $message->to($emails);
                        $message->subject('Perubahan Jadwal');
                    });
                }
            } catch(\Exception $e){
                Log::error('Gagal mengirim email notifikasi jadwal (ubah): ' . $e->getMessage());
            }
        }
        return redirect()->route('jadwal')->with('success', 'Data jadwal berhasil diubah.');
    }

    public function hapusJadwal(Request $request){
        $validate = Validator::make($request->all(), [
            'id_jadwal' => 'required|exists:jadwals,id'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $jadwal = Jadwal::findOrFail($request->id_jadwal);
        $simpan = $jadwal->delete();

        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menghapus data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menghapus data jadwal tanggal: ' . $jadwal->tanggal;
        $notif->kategori = 'jadwal';
        $notif->save();
        if($notif->kategori === 'jadwal'){
            try{
                $emails = [];
                $emails = array_merge($emails, User::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_merge($emails, Pelatih::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_merge($emails, Atlet::whereNotNull('email')->pluck('email')->toArray());
                $emails = array_filter(array_unique($emails));
                if(!empty($emails)){
                    $dataMail = ['keterangan' => $notif->keterangan];
                    Mail::send('emails.jadwal_notification', $dataMail, function($message) use ($emails){
                        $message->to($emails);
                        $message->subject('Penghapusan Jadwal');
                    });
                }
            } catch(\Exception $e){
                Log::error('Gagal mengirim email notifikasi jadwal (hapus): ' . $e->getMessage());
            }
        }
        return redirect()->route('jadwal')->with('success', 'Data jadwal berhasil dihapus.');
    }

public function user(){
    $user = auth()->user();
    
    // Hanya admin (role 0) dan petugas (role 1) yang bisa akses
    if ($user->role == 2 || $user->role == 3) { // Kecamatan atau Nagari
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
    
    $dataPegawai = User::all();
    return view('pegawai.user', compact('dataPegawai'));
}

public function tambahUser(){
    $user = auth()->user();
    
    // Hanya admin (role 0) dan petugas (role 1) yang bisa akses
    if ($user->role == 2 || $user->role == 3) { // Kecamatan atau Nagari
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    $kecamatans = Kecamatan::all();
    $nagaris = Nagari::all();

    // jika tabel kecamatan/nagari tidak diisi, fallback ke CSV/XLSX yang ditempatkan di folder csv/
    if ($kecamatans->isEmpty() || $nagaris->isEmpty()) {
        $csvData = $this->loadGeodataFromCsv();
        $kecamatans = collect($csvData['kecamatans']);
        $nagaris = collect($csvData['nagaris']);
    }

    return view('insert.user', compact('kecamatans', 'nagaris'));
}

public function simpanUser(Request $request){
    $user = auth()->user();
    
    // Hanya admin (role 0) dan petugas (role 1) yang bisa akses
    if ($user->role == 2 || $user->role == 3) { // Kecamatan atau Nagari
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke fitur ini.');
    }

    $validate = Validator::make($request->all(), [
        'nama' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => 'required|in:0,1,2,3,4,5',
        // kecamatan_id / nagari_id may come from CSV fallback or non-numeric codes — resolve below
        'kecamatan_id' => 'nullable',
        'nagari_id' => 'nullable'
    ]);

    if($validate->fails()){
        return redirect()->back()->withErrors($validate)->withInput();
    }

    $data = $validate->validated();

    $user = new User();
    $user->nama = $data['nama'];
    $user->email = $data['email'];
    $user->password = bcrypt($data['password']);
    $user->role = $data['role'];
    // assign area if role requires it
    // resolve possibly non-numeric inputs (from CSV fallback) into DB ids
    $resolvedKecId = null;
    if ($data['role'] == 2 && isset($data['kecamatan_id']) && $data['kecamatan_id']) {
        $resolvedKecId = $this->resolveKecamatanId($data['kecamatan_id']);
        $user->kecamatan_id = $resolvedKecId;
    }

    if ($data['role'] == 3 && isset($data['nagari_id']) && $data['nagari_id']) {
        $resolvedNagId = $this->resolveNagariId($data['nagari_id'], $resolvedKecId);
        $user->nagari_id = $resolvedNagId;
    }

    $simpan = $user->save();

    if(!$simpan){
        return redirect()->back()->with('error', 'Gagal menambahkan user, silakan periksa kembali inputan Anda.');
    }

    $notif = new Notif();
    $pegawai = auth()->user()->nama;
    $notif->keterangan = 'Pegawai (' . $pegawai . ') menambahkan data pegawai baru: ' . $data['nama'];
    $notif->kategori = 'anggota';
    $notif->save();
    return redirect()->route('user')->with('success', 'User berhasil ditambahkan.');
}

public function ubahUser(Request $request){
    $user = auth()->user();
    
    // Hanya admin (role 0) dan petugas (role 1) yang bisa akses
    if ($user->role == 2 || $user->role == 3) { // Kecamatan atau Nagari
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke fitur ini.');
    }

    if(!$request->id_user){
        return redirect()->back()->with('error', 'ID user tidak ditemukan.');
    }
    $dataUser = User::findOrFail($request->id_user);
    $kecamatans = Kecamatan::all();
    $nagaris = Nagari::all();

    if ($kecamatans->isEmpty() || $nagaris->isEmpty()) {
        $csvData = $this->loadGeodataFromCsv();
        $kecamatans = collect($csvData['kecamatans']);
        $nagaris = collect($csvData['nagaris']);
    }
    return view('insert.updateUser', compact('dataUser', 'kecamatans', 'nagaris'));
}

public function simpanUbahUser(Request $request){
    $user = auth()->user();
    
    // Hanya admin (role 0) dan petugas (role 1) yang bisa akses
    if ($user->role == 2 || $user->role == 3) { // Kecamatan atau Nagari
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke fitur ini.');
    }

    $validate = Validator::make($request->all(), [
        'id_user' => 'required|exists:users,id',
        'nama' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,'.$request->id_user,
        'password' => 'nullable|string|min:8',
        'role' => 'required|in:0,1,2,3,4,5',
        'kecamatan_id' => 'nullable',
        'nagari_id' => 'nullable'
    ]);

    if($validate->fails()){
        return redirect()->route('user')->with('error', 'Gagal mengubah user, silakan periksa kembali inputan Anda.');
    }

    $data = $validate->validated();

    $user = User::findOrFail($data['id_user']);
    $user->nama = $data['nama'];
    $user->email = $data['email'];
    if($data['password']){
        $user->password = bcrypt($data['password']);
    }
    $user->role = $data['role'];
    // update wilayah if applicable; resolve potentially non-numeric CSV values
    if ($data['role'] == 2) {
        if (isset($data['kecamatan_id']) && $data['kecamatan_id']) {
            $user->kecamatan_id = $this->resolveKecamatanId($data['kecamatan_id']);
        } else {
            $user->kecamatan_id = null;
        }
        // clear nagari when role is kecamatan
        $user->nagari_id = null;
    } elseif ($data['role'] == 3) {
        // if kecamatan provided, resolve it first (used as parent for nagari creation)
        $resolvedKec = null;
        if (isset($data['kecamatan_id']) && $data['kecamatan_id']) {
            $resolvedKec = $this->resolveKecamatanId($data['kecamatan_id']);
            $user->kecamatan_id = $resolvedKec;
        } else {
            $user->kecamatan_id = null;
        }
        if (isset($data['nagari_id']) && $data['nagari_id']) {
            $user->nagari_id = $this->resolveNagariId($data['nagari_id'], $resolvedKec);
        } else {
            $user->nagari_id = null;
        }
    } else {
        $user->kecamatan_id = null;
        $user->nagari_id = null;
    }
    $simpan = $user->save();

    if(!$simpan){
        return redirect()->route('user')->with('error', 'Gagal mengubah user, silakan periksa kembali inputan Anda.');
    }

    $notif = new Notif();
    $pegawai = auth()->user()->nama;
    $notif->keterangan = 'Pegawai (' . $pegawai . ') mengubah data pegawai: ' . $data['nama'];
    $notif->kategori = 'anggota';
    $notif->save();
    return redirect()->route('user')->with('success', 'User berhasil diubah.');
}

public function hapusUser(Request $request){
    $user = auth()->user();
    
    // Hanya admin (role 0) dan petugas (role 1) yang bisa akses
    if ($user->role == 2 || $user->role == 3) { // Kecamatan atau Nagari
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke fitur ini.');
    }

    $validate = Validator::make($request->all(), [
        'id_user' => 'required|exists:users,id'
    ]);

    if($validate->fails()){
        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }

    $user = User::findOrFail($request->id_user);
    $simpan = $user->delete();

    if(!$simpan){
        return redirect()->back()->with('error', 'Gagal menghapus user, silakan periksa kembali inputan Anda.');
    }

    $notif = new Notif();
    $pegawai = auth()->user()->nama;
    $notif->keterangan = 'Pegawai (' . $pegawai . ') menghapus data pegawai: ' . $user->nama;
    $notif->kategori = 'anggota';
    $notif->save();
    return redirect()->route('user')->with('success', 'User berhasil dihapus.');
}

    public function notifikasi(Request $request){
        $validate = Validator::make($request->all(), [
            'notif_id' => 'required|array',
            'notif_id.*' => 'exists:notifs,id'
        ]);

        $notifs = $validate->validated();
        $update = Notif::whereIn('id', $notifs['notif_id'])->update(['is_read' => true]);

        return redirect()->back();
    }

    // show import form
    public function importAtletForm(){
        return view('pegawai.import_atlet');
    }

    // handle import submit
    public function importAtletSubmit(Request $request){
        $validate = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'File tidak valid, gunakan file Excel .xlsx atau .xls');
        }

        try{
            $import = new AtletImport();
            Excel::import($import, $request->file('file'));
        } catch (\Exception $e){
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }

        $inserted = $import->inserted ?? 0;
        $skipped = $import->skipped ?? 0;

        return redirect()->route('data.atlet')->with('success', "Import selesai. Dimasukkan: {$inserted}, Dilewati: {$skipped}");
    }

    public function importPelatihForm(){
        return view('pegawai.import_pelatih');
    }

    // handle import submit
    public function importPelatihSubmit(Request $request){
        $validate = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'File tidak valid, gunakan file Excel .xlsx atau .xls');
        }

        try{
            $import = new PelatihImport();
            Excel::import($import, $request->file('file'));
        } catch (\Exception $e){
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }

        $inserted = $import->inserted ?? 0;
        $skipped = $import->skipped ?? 0;

        return redirect()->route('data.pelatih')->with('success', "Import selesai. Dimasukkan: {$inserted}, Dilewati: {$skipped}");
    }

    /**
     * Load kecamatan and nagari lists from csv/xls/xlsx files in /csv folder.
     * Returns ['kecamatans' => [...], 'nagaris' => [...]] where each item is an object with id and name.
     */
    private function loadGeodataFromCsv()
    {
        $base = base_path('csv');
        $kecPath = $base . DIRECTORY_SEPARATOR . 'kecamatan';
        $nagPath = $base . DIRECTORY_SEPARATOR . 'nagari';

        $collectFiles = function ($dir) {
            $files = [];
            if (!is_dir($dir)) return $files;
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            foreach ($it as $f) {
                if (!$f->isFile()) continue;
                $ext = strtolower(pathinfo($f->getFilename(), PATHINFO_EXTENSION));
                if (in_array($ext, ['csv','xls','xlsx'])) {
                    $files[] = $f->getPathname();
                }
            }
            return $files;
        };

        $kecFiles = $collectFiles($kecPath);
        $nagFiles = $collectFiles($nagPath);

        $parseRowsFromFile = function ($file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $rows = [];
            if (!is_file($file) || !is_readable($file)) return $rows;

            if (in_array($ext, ['xls','xlsx'])) {
                try {
                    // Use PhpSpreadsheet via the Excel facade when available
                    $sheets = Excel::toArray(null, $file);
                    if (is_array($sheets) && count($sheets) > 0) {
                        $sheet = $sheets[0] ?? [];
                        foreach ($sheet as $data) {
                            $rows[] = $data;
                        }
                    }
                } catch (\Exception $e) {
                    return [];
                }
            } else {
                if (($handle = fopen($file, 'r')) !== false) {
                    while (($data = fgetcsv($handle, 0, ',')) !== false) {
                        if (count($data) === 1 && trim($data[0]) === '') continue;
                        $rows[] = $data;
                    }
                    fclose($handle);
                }
            }

            $assoc = [];
            $headers = null;
            foreach ($rows as $data) {
                if (!$headers) {
                    $isHeader = false;
                    foreach ($data as $cell) {
                        if (is_string($cell) && preg_match('/[a-zA-Z]/', $cell)) { $isHeader = true; break; }
                    }
                    if ($isHeader) {
                        $headers = array_map(function($h){ return trim(strtolower((string)$h)); }, $data);
                        continue;
                    } else {
                        $headers = array_map(function($i){ return 'col_' . $i; }, array_keys($data));
                    }
                }
                $row = [];
                foreach ($data as $i => $value) {
                    $key = $headers[$i] ?? 'col_' . $i;
                    $row[$key] = is_scalar($value) ? trim((string)$value) : $value;
                }
                $assoc[] = $row;
            }
            return $assoc;
        };

        $kecList = [];
        foreach ($kecFiles as $file) {
            $rows = $parseRowsFromFile($file);
            foreach ($rows as $r) {
                $id = $r['id'] ?? ($r['kode'] ?? null);
                $name = $r['nama'] ?? ($r['name'] ?? ($r['kecamatan'] ?? null));
                if (!$name) {
                    $first = reset($r);
                    $name = $first !== false ? $first : null;
                }
                if (!$name) continue;
                $kecList[] = (object)['id' => $id, 'name' => $name];
            }
        }

        $nagList = [];
        foreach ($nagFiles as $file) {
            $rows = $parseRowsFromFile($file);
            foreach ($rows as $r) {
                $id = $r['id'] ?? ($r['kode'] ?? null);
                $name = $r['nama'] ?? ($r['name'] ?? ($r['nagari'] ?? null));
                if (!$name) {
                    $first = reset($r);
                    $name = $first !== false ? $first : null;
                }
                if (!$name) continue;
                $parent = $r['kecamatan'] ?? ($r['kecamatan_name'] ?? ($r['kecamatan_id'] ?? ($r['kec'] ?? null)));
                $nagList[] = (object)['id' => $id, 'name' => $name, 'kecamatan' => $parent];
            }
        }

        return ['kecamatans' => $kecList, 'nagaris' => $nagList];
    }

    /**
     * Resolve a kecamatan identifier (id, kode, or name) to a DB id. Create if missing.
     * @param mixed $input
     * @return int|null
     */
    public function resolveKecamatanId($input)
    {
        if (!$input) return null;
        // numeric id
        if (is_numeric($input)) {
            $k = Kecamatan::find($input);
            if ($k) return $k->id;
        }
        // try by code or name
        $k = Kecamatan::where('kode_kecamatan', $input)->orWhere('nama_kecamatan', $input)->first();
        if ($k) return $k->id;
        // create a new kecamatan record
        $new = new Kecamatan();
        // set fields conservatively
        $new->nama_kecamatan = (string)$input;
        $new->kode_kecamatan = 'K' . substr(md5((string)$input . time()), 0, 8);
        $new->save();
        return $new->id;
    }

    /**
     * Resolve a nagari identifier (id, kode, or name) to a DB id. Create if missing.
     * @param mixed $input
     * @param int|null $kecamatanId
     * @return int|null
     */
    public function resolveNagariId($input, $kecamatanId = null)
    {
        if (!$input) return null;
        if (is_numeric($input)) {
            $n = Nagari::find($input);
            if ($n) return $n->id;
        }
        // try by code or name
        $n = Nagari::where('kode_nagari', $input)->orWhere('nama_nagari', $input)->first();
        if ($n) return $n->id;
        // create new nagari
        $new = new Nagari();
        $new->nama_nagari = (string)$input;
        $new->kode_nagari = 'N' . substr(md5((string)$input . time()), 0, 8);
        if ($kecamatanId) $new->kecamatan_id = $kecamatanId;
        $new->save();
        return $new->id;
    }
}
