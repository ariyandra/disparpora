<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Asesmen;
use Illuminate\Http\Request;
use App\Models\Atlet;
use App\Models\Pelatih;
use App\Models\Lapangan;
use App\Models\Cabor;
use App\Models\Notif;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class pelatihController extends Controller
{
    public function index(){
        $jml_atlit = Atlet::count();
        $jml_pelatih = Pelatih::count();
        $jml_lapangan = Lapangan::count();
        $jml_cabor = Cabor::count();
        $notifikasi = Notif::where('is_read', false)->whereIn('kategori', ['jadwal', 'lapangan', 'asesmen', 'absensi'])->take(5)->get();
        return view('pelatih.dashboard', compact('jml_atlit', 'jml_pelatih', 'jml_lapangan', 'jml_cabor', 'notifikasi'));
    }

    public function atlet(){
        $dataAtlet = Atlet::all();
        return view('pelatih.atlet', compact('dataAtlet'));
    }

    public function cabor(){
        $dataCabor = Cabor::all();
        return view('pelatih.cabor', compact('dataCabor'));
    }

    public function lapangan(){
        $dataLapangan = Lapangan::all();
        return view('pelatih.lapangan', compact('dataLapangan'));
    }

    public function asesmen(){
        $dataAsesmen = Asesmen::all();
        return view('pelatih.asesmen', compact('dataAsesmen'));
    }

    public function tambahAsesmen(){
        $dataAtlet = Atlet::all();
        $dataPelatih = Pelatih::all();
        return view('insert.asesmen', compact('dataAtlet', 'dataPelatih'));
    }

    public function simpanAsesmen(Request $request){
        $validate = Validator::make($request->all(), [
            'nama_atlet' => 'required|exists:atlets,id',
            'tanggal' => 'required|date',
            'aspek_fisik' => 'required|string|max:5',
            'aspek_teknik' => 'required|string|max:5',
            'aspek_sikap' => 'required|string|max:5',
            'keterangan' => 'required|string',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data yang anda masukkan tidak valid. Silakan periksa kembali.');
        }

        $data = $validate->validated();
        $asesmen = new Asesmen();
        $asesmen->id_atlet = $data['nama_atlet'];
        $asesmen->id_pelatih = Auth::guard('pelatih')->user()->id;
        $asesmen->tanggal_asesmen = $data['tanggal'];
        $asesmen->aspek_fisik = $data['aspek_fisik'];
        $asesmen->aspek_teknik = $data['aspek_teknik'];
        $asesmen->aspek_sikap = $data['aspek_sikap'];
        $asesmen->keterangan = $data['keterangan'];
        $simpan = $asesmen->save();
        if(!$simpan){
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }

        $notif = new Notif();
        $atlet = Atlet::where('id', $data['nama_atlet'])->first();
        $notif->kategori = 'asesmen';
        $notif->keterangan = 'Asesmen untuk '.$atlet->nama.' telah ditambahkan.';
        $notif->save();
        return redirect()->route('asesmen.pelatih')->with('success', 'Data asesmen berhasil disimpan.');
    }

    public function absensi(){
        $dataAbsensi = Absensi::all();
        return view('pelatih.absensi', compact('dataAbsensi'));
    }

    public function isiAbsensi(){
        $dataAtlet = Atlet::all();
        $dataJadwal = Jadwal::all();
        return view('insert.absensi', compact('dataAtlet'));
    }

    public function simpanAbsensi(Request $request){
        $validate = Validator::make($request->all(), [
            'nama_atlet' => 'required|string|max:255',
            'atlet_id' => 'required|exists:atlets,id',
            'jadwal' => 'required|date_format:H:i',
            'status' => 'required|string|max:15',
            'tanggal_absen' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data yang anda masukkan tidak valid. Silakan periksa kembali.');
        }

        $data = $validate->validated();
        $absensi = new Absensi();
        $absensi->id_atlet = $data['atlet_id'];
        $absensi->jadwal = $data['jadwal'];
        $absensi->tanggal_absen = $data['tanggal_absen'];
        $absensi->status = $data['status'];
        $absensi->keterangan = $data['keterangan'];

        $simpan = $absensi->save();
        if(!$simpan){
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }

        $notif = new Notif();
        $atlet = Atlet::where('id', $data['atlet_id'])->first();
        $notif->kategori = 'absensi';
        $notif->keterangan = 'Absensi untuk '.$atlet->nama.' telah ditambahkan.';
        $notif->save();
        return redirect()->route('absensi.pelatih')->with('success', 'Data absensi berhasil disimpan.');
    }

    public function ubahAbsensi(Request $request){
    // Accept id from query string (?id=4) or from request body (id_absensi) for compatibility
    $id = $request->query('id') ?? $request->route('id') ?? $request->input('id_absensi');

        $validate = Validator::make(['id_absensi' => $id], [
            'id_absensi' => 'required|exists:absensis,id',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $dataAbsensi = Absensi::where('id', $id)->first();
        $dataAtlet = Atlet::all();
        return view('insert.ubahAbsensi', compact( 'dataAbsensi', 'dataAtlet'));
    }

    public function simpanUbahAbsensi(Request $request){
        $validate = Validator::make($request->all(), [
            'id_absensi' => 'required|exists:absensis,id',
            'atlet_id' => 'required|exists:atlets,id',
            'jadwal' => 'required|date_format:H:i',
            'status' => 'required|string|max:15',
            'tanggal_absen' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        if($validate->fails()){
            return redirect()->route('absensi.pelatih')->with('error', 'Data yang anda masukkan tidak valid. Silakan periksa kembali.');
        }

        $data = $validate->validated();
        $absensi = Absensi::where('id', $data['id_absensi'])->first();
        if(!$absensi){
            return redirect()->route('pelatih.absensi')->with('error', 'Data absensi tidak ditemukan.');
        }

        $absensi->id_atlet = $data['atlet_id'];
        $absensi->jadwal = $data['jadwal'];
        $absensi->tanggal_absen = $data['tanggal_absen'];
        $absensi->status = $data['status'];
        $absensi->keterangan = $data['keterangan'];

        $simpan = $absensi->save();
        if(!$simpan){
            return redirect()->route('pelatih.absensi')->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
        else{
            $notif = new Notif();
            $atlet = Atlet::where('id', $data['atlet_id'])->first();
            $notif->kategori = 'absensi';
            $notif->keterangan = 'Absensi untuk '.$atlet->nama.' telah ditambahkan.';
            $notif->save();
            return redirect()->route('absensi.pelatih')->with('success', 'Data absensi berhasil diubah.');
        }
    }

    public function hapusAbsensi(Request $request){
        $validate = Validator::make($request->all(), [
            'id_absensi' => 'required|exists:absensis,id',
        ]);

        if($validate->fails()){
            return redirect()->route('pelatih.absensi')->with('error', 'Data tidak ditemukan.');
        }

        $absensi = Absensi::where('id', $request->id_absensi)->first();
        if(!$absensi){
            return redirect()->route('pelatih.absensi')->with('error', 'Data absensi tidak ditemukan.');
        }

        $hapus = $absensi->delete();
        if(!$hapus){
            return redirect()->route('pelatih.absensi')->with('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.');
        }
        else{
            return redirect()->route('absensi.pelatih')->with('success', 'Data absensi berhasil dihapus.');
        }
    }

    public function jadwal(){
        $dataJadwal = Jadwal::all();
        return view('pelatih.jadwal', compact('dataJadwal'));
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
}
