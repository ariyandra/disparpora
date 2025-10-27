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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AtletImport;
use App\Imports\PelatihImport;

class adminController extends Controller
{
    public function index(){
        $jml_atlit = Atlet::count();
        $jml_pelatih = Pelatih::count();
        $jml_lapangan = Lapangan::count();
        $jml_cabor = Cabor::count();
        $notif = Notif::where('is_read', false)->take(5)->get();
        return view('pegawai.dashboard', compact('jml_atlit', 'jml_pelatih', 'jml_lapangan', 'jml_cabor', 'notif'));
    }

    public function pagePelatih(){
        $dataPelatih = Pelatih::all();
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
        $dataAtlet = Atlet::all();
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
        $dataCabor = Cabor::all();
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
        $cabor = new Cabor();
        $cabor->nama_cabor = $data['nama_cabor'];
        $cabor->deskripsi = $data['deskripsi'];
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
        $dataLapangan = Lapangan::all();
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
        $dataJadwal = Jadwal::all();
        return view('pegawai.jadwal', compact('dataJadwal'));
    }

    public function jadwalBaru(){
        $dataCabor = Cabor::all();
        $dataLapangan = Lapangan::all();
        return view('insert.jadwal', compact('dataCabor', 'dataLapangan'));
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
        $jadwal->keterangan = $data['keterangan'];
        $simpan = $jadwal->save();

        if(!$simpan){
            return redirect()->back()->with('error', 'Gagal menyimpan data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') menambahkan data jadwal baru: ' . $data['tanggal'];
        $notif->kategori = 'jadwal';
        $notif->save();

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
        $jadwal->keterangan = $data['keterangan'];
        $simpan = $jadwal->save();

        if(!$simpan){
            return redirect()->route('jadwal')->with('error', 'Gagal mengubah data, silakan periksa kembali inputan Anda.');
        }

        $notif = new Notif();
        $pegawai = auth()->user()->nama;
        $notif->keterangan = 'Pegawai (' . $pegawai . ') mengubah data jadwal tanggal: ' . $data['tanggal'];
        $notif->kategori = 'jadwal';
        $notif->save();
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
        return redirect()->route('jadwal')->with('success', 'Data jadwal berhasil dihapus.');
    }

    public function user(){
        $dataPegawai = User::all();
        return view('pegawai.user', compact('dataPegawai'));
    }

    public function tambahUser(){
        return view('insert.user');
    }

    public function simpanUser(Request $request){
        $validate = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:0,1'
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Gagal menambahkan user, silakan periksa kembali inputan Anda.');
        }

        $data = $validate->validated();

        $user = new User();
        $user->nama = $data['nama'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->role = $data['role'];
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
        if(!$request->id_user){
            return redirect()->back()->with('error', 'ID user tidak ditemukan.');
        }
        $dataUser = User::findOrFail($request->id_user);
        return view('insert.updateUser', compact('dataUser'));
    }

    public function simpanUbahUser(Request $request){
        $validate = Validator::make($request->all(), [
            'id_user' => 'required|exists:users,id',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$request->id_user,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:0,1'
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
}
