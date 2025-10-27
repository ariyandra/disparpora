<?php

namespace App\Http\Controllers;

use App\Models\Asesmen;
use App\Models\Atlet;
use App\Models\Cabor;
use App\Models\Jadwal;
use App\Models\Notif;
use App\Models\Absensi;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class atletController extends Controller
{
    public function index(){
        $dataAtlet = auth()->guard('atlet')->user();
        $notifikasi = Notif::where('is_read', false)->whereIn('kategori', ['jadwal', 'lapangan', 'asesmen', 'absensi'])->take(5)->get();
        return view('atlet.dashboard', compact('dataAtlet', 'notifikasi'));
    }

    public function editBiodata(){
        $dataAtlet = auth()->guard('atlet')->user();
        $dataCabor = Cabor::all();
        return view('atlet.editBiodata', compact('dataAtlet', 'dataCabor'));
    }

    public function simpanUpdateBiodata(Request $request){
        $validate = Validator::make($request->all(), [
            'id_atlet' => 'required|exists:atlets,id',
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'jenis_kelamin' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'cabang_olahraga' => 'required|exists:cabors,id',
            'tanggal_lahir' => 'required|date',
            'tanggal_gabung' => 'required|date',
            'status' => 'required|string',
        ]);

        if($validate->fails()){
            return redirect()->route('dashboard.atlet')->with('error', 'Periksa kembali data yang anda masukkan! Email tidak boleh sama');
        }

        $atlet = Atlet::findOrFail($request->id_atlet);

        $emailExists = Atlet::where('email', $request->email)->where('id', '!=', $request->id_atlet)->exists();
        if($emailExists){
            return redirect()->route('dashboard.atlet')->with('error', 'Periksa kembali data yang anda masukkan! Email tidak boleh sama');
        }

        $update = $atlet->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_telp' => $request->no_hp,
            'id_cabor' => $request->cabang_olahraga,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tanggal_gabung' => $request->tanggal_gabung,
            'status' => $request->status,
        ]);

        if(!$update){
            return redirect()->route('dashboard.atlet')->with('error', 'Gagal memperbarui data!');
        } else {
            return redirect()->route('dashboard.atlet')->with('success', 'Berhasil memperbarui data!');
        }
    }

    public function cabor(){
        $dataCabor = Cabor::all();
        return view('atlet.cabor', compact('dataCabor'));
    }

    public function lapangan(){
        $dataLapangan = Lapangan::all();
        return view('atlet.lapangan', compact('dataLapangan'));
    }

    public function asesmen(){
        $asesmen = Asesmen::where('id_atlet', auth()->guard('atlet')->user()->id)->get();
        return view('atlet.asesmen', compact('asesmen'));
    }

    public function absensi(){
        $absensi = Absensi::where('id_atlet', auth()->guard('atlet')->user()->id)->get();
        return view('atlet.absensi', compact('absensi'));
    }

    public function jadwal(){
        $dataJadwal = Jadwal::all();
        return view('atlet.jadwal', compact('dataJadwal'));
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
