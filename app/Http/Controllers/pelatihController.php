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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;

class pelatihController extends Controller
{
    public function index(){
        $jml_atlit = Atlet::where('status_verifikasi', 'approved')->count();
        $jml_pelatih = Pelatih::where('status_verifikasi', 'approved')->count();
        $jml_lapangan = Lapangan::where('status_verifikasi', 'approved')->count();
        $jml_cabor = Cabor::where('status_verifikasi', 'approved')->count();
        $notifikasi = Notif::where('is_read', false)->whereIn('kategori', ['jadwal', 'lapangan', 'asesmen', 'absensi'])->take(5)->get();
        return view('pelatih.dashboard', compact('jml_atlit', 'jml_pelatih', 'jml_lapangan', 'jml_cabor', 'notifikasi'));
    }

    public function atlet(){
        $dataAtlet = Atlet::with('documents')->where('status_verifikasi', 'approved')->get();
        return view('pelatih.atlet', compact('dataAtlet'));
    }

    public function cabor(){
        $dataCabor = Cabor::where('status_verifikasi', 'approved')->get();
        return view('pelatih.cabor', compact('dataCabor'));
    }

    public function lapangan(){
        $dataLapangan = Lapangan::where('status_verifikasi', 'approved')->get();
        return view('pelatih.lapangan', compact('dataLapangan'));
    }

    public function asesmen(Request $request){
    $pelatihUser = Auth::guard('pelatih')->user();

        $start = $request->query('start_date');
        $end = $request->query('end_date');
        if(!$start || !$end){
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }
        $atletId = $request->query('atlet_id');

        $asesmenQuery = Asesmen::with(['atlet'])
            ->whereBetween('tanggal_asesmen', [$start, $end]);

        if($atletId){
            $asesmenQuery->where('id_atlet', $atletId);
        }

        // Jika pelatih punya id_cabor, batasi ke cabor tersebut
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            $asesmenQuery->whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_cabor', $pelatihUser->id_cabor);
            });
        }

    $asesmenRows = $asesmenQuery->get();

        // Jika hasil kosong karena filter cabor, fallback ke semua dan beri pesan
        if($pelatihUser && !empty($pelatihUser->id_cabor) && $asesmenRows->isEmpty()){
            session()->flash('warning', 'Data asesmen untuk cabang Anda tidak ditemukan; menampilkan semua asesmen sebagai fallback.');
            $asesmenQuery = Asesmen::with(['atlet'])->whereBetween('tanggal_asesmen', [$start, $end]);
            if($atletId){ $asesmenQuery->where('id_atlet', $atletId); }
            $asesmenRows = $asesmenQuery->get();
        }

    // assign filtered asesmen collection to pass to the view
    $dataAsesmen = $asesmenRows;
    $grouped = $asesmenRows->groupBy('id_atlet');
        $rekap = [];
        foreach($grouped as $aid => $rows){
            $rekap[] = [
                'atlet_nama' => optional($rows->first()->atlet)->nama,
                'jumlah' => $rows->count(),
                'avg_fisik' => round($rows->avg('aspek_fisik'), 2),
                'avg_teknik' => round($rows->avg('aspek_teknik'), 2),
                'avg_sikap' => round($rows->avg('aspek_sikap'), 2),
                'last_asesmen' => $rows->sortByDesc('tanggal_asesmen')->first()->tanggal_asesmen,
            ];
        }

        $filters = [
            'start_date' => $start,
            'end_date' => $end,
            'atlet_id' => $atletId,
        ];

        // Batasi daftar atlet sesuai cabor pelatih, dengan fallback ke semua jika kosong
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            $atlets = Atlet::where('id_cabor', $pelatihUser->id_cabor)->get();
            if($atlets->isEmpty()){
                session()->flash('warning', 'Tidak ditemukan atlet pada cabang Anda; menampilkan semua atlet sebagai fallback.');
                $atlets = Atlet::all();
            }
        } else {
            $atlets = Atlet::all();
        }

    return view('pelatih.asesmen', compact('dataAsesmen', 'rekap', 'filters', 'atlets'));
    }

    public function tambahAsesmen(){
        $pelatihUser = Auth::guard('pelatih')->user();
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            $dataAtlet = Atlet::where('id_cabor', $pelatihUser->id_cabor)->get();
            if($dataAtlet->isEmpty()){
                session()->flash('warning', 'Tidak ditemukan atlet pada cabang Anda; menampilkan semua atlet sebagai fallback.');
                $dataAtlet = Atlet::all();
            }
        } else {
            $dataAtlet = Atlet::all();
        }
        $dataPelatih = Pelatih::all();
        return view('insert.asesmen', compact('dataAtlet', 'dataPelatih'));
    }

    public function simpanAsesmen(Request $request){
        $validate = Validator::make($request->all(), [
            'nama_atlet' => 'required|exists:atlets,id',
            'tanggal' => 'required|date',
            'aspek_fisik' => 'required|numeric|min:0|max:100',
            'aspek_teknik' => 'required|numeric|min:0|max:100',
            'aspek_sikap' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data yang anda masukkan tidak valid. Silakan periksa kembali.');
        }

        $data = $validate->validated();
        $pelatihUser = Auth::guard('pelatih')->user();
        // jika pelatih punya id_cabor, pastikan atlet milik cabor yang sama
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            if(!Atlet::where('id', $data['nama_atlet'])->where('id_cabor', $pelatihUser->id_cabor)->exists()){
                return redirect()->back()->with('error', 'Anda tidak diperbolehkan menambahkan asesmen untuk atlet di luar cabang Anda.');
            }
        }
        $asesmen = new Asesmen();
        $asesmen->id_atlet = $data['nama_atlet'];
        $asesmen->id_pelatih = Auth::guard('pelatih')->user()->id;
        $asesmen->tanggal_asesmen = $data['tanggal'];
        $asesmen->aspek_fisik = $data['aspek_fisik'];
        $asesmen->aspek_teknik = $data['aspek_teknik'];
        $asesmen->aspek_sikap = $data['aspek_sikap'];
        $asesmen->keterangan = $data['keterangan'] ?? '';
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

    public function absensi(Request $request){
        $pelatihUser = Auth::guard('pelatih')->user();
        // Jika pelatih memiliki id_cabor, batasi tampilan absensi hanya untuk atlet cabor tersebut
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            $dataAbsensi = Absensi::whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_cabor', $pelatihUser->id_cabor);
            })->get();
        } else {
            $dataAbsensi = Absensi::all();
        }

        $start = $request->query('start_date');
        $end = $request->query('end_date');
        if(!$start || !$end){
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }
        $atletId = $request->query('atlet_id');

        $absensiQuery = Absensi::with(['atlet'])
            ->whereBetween('tanggal_absen', [$start, $end]);
        if(isset($pelatihUser) && !empty($pelatihUser->id_cabor)){
            $absensiQuery->whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_cabor', $pelatihUser->id_cabor);
            });
        }
        if($atletId){
            $absensiQuery->where('id_atlet', $atletId);
        }

        $absensiRows = $absensiQuery->get();
        $grouped = $absensiRows->groupBy('id_atlet');
        $rekap = [];
        foreach($grouped as $aid => $rows){
            $total = $rows->count();
            $hadir = $rows->where('status', 'Hadir')->count();
            $sakit = $rows->where('status', 'Sakit')->count();
            $izin = $rows->where('status', 'Izin')->count();
            $alpa = $rows->where('status', 'Alpa')->count();
            $rekap[] = [
                'atlet_nama' => optional($rows->first()->atlet)->nama,
                'total' => $total,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'persen_hadir' => $total ? round(($hadir/$total)*100, 2) : null,
            ];
        }

        $filters = [
            'start_date' => $start,
            'end_date' => $end,
            'atlet_id' => $atletId,
        ];
        // Batasi daftar atlet di filter/selector sesuai cabor pelatih
        if(isset($pelatihUser) && !empty($pelatihUser->id_cabor)){
            $atlets = Atlet::where('id_cabor', $pelatihUser->id_cabor)->get();
        } else {
            $atlets = Atlet::all();
        }

        return view('pelatih.absensi', compact('dataAbsensi', 'rekap', 'filters', 'atlets'));
    }

    public function exportAsesmenRekapCsv(Request $request){
        $start = $request->query('start_date');
        $end = $request->query('end_date');
        if(!$start || !$end){
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }
        $atletId = $request->query('atlet_id');
        $pelatihUser = Auth::guard('pelatih')->user();
        $asesmenQuery = Asesmen::with(['atlet'])
            ->whereBetween('tanggal_asesmen', [$start, $end]);
        if($atletId){ $asesmenQuery->where('id_atlet', $atletId); }
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            $asesmenQuery->whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_cabor', $pelatihUser->id_cabor);
            });
        }
        $asesmenRows = $asesmenQuery->get();
        // fallback jika kosong
        if($pelatihUser && !empty($pelatihUser->id_cabor) && $asesmenRows->isEmpty()){
            session()->flash('warning', 'Data asesmen untuk cabang Anda tidak ditemukan; menampilkan semua asesmen sebagai fallback.');
            $asesmenQuery = Asesmen::with(['atlet'])->whereBetween('tanggal_asesmen', [$start, $end]);
            if($atletId){ $asesmenQuery->where('id_atlet', $atletId); }
            $asesmenRows = $asesmenQuery->get();
        }
        $asesmenRows = $asesmenRows->groupBy('id_atlet');

        $lines = [];
        $header = ['Atlet','Jumlah','Rata2 Fisik','Rata2 Teknik','Rata2 Sikap','Terakhir Asesmen'];
        $lines[] = implode(',', array_map(function($v){ return '"'.str_replace('"','""',$v).'"'; }, $header));
        foreach($asesmenRows as $rows){
            $row = [
                optional($rows->first()->atlet)->nama,
                $rows->count(),
                round($rows->avg('aspek_fisik'),2),
                round($rows->avg('aspek_teknik'),2),
                round($rows->avg('aspek_sikap'),2),
                $rows->sortByDesc('tanggal_asesmen')->first()->tanggal_asesmen,
            ];
            $lines[] = implode(',', array_map(function($v){ return '"'.str_replace('"','""',(string)$v).'"'; }, $row));
        }
        $csv = implode("\r\n", $lines);
        $filename = 'rekap_asesmen_pelatih_'.str_replace('-', '', $start).'_'.str_replace('-', '', $end).'.csv';
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function exportAbsensiRekapCsv(Request $request){
        $start = $request->query('start_date');
        $end = $request->query('end_date');
        if(!$start || !$end){
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }
        $atletId = $request->query('atlet_id');
        $pelatihUser = Auth::guard('pelatih')->user();
        $absensiQuery = Absensi::with(['atlet'])
            ->whereBetween('tanggal_absen', [$start, $end]);
        if($atletId){ $absensiQuery->where('id_atlet', $atletId); }
        // Batasi export ke cabor pelatih jika pelatih punya id_cabor
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            $absensiQuery->whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_cabor', $pelatihUser->id_cabor);
            });
        }
        $grouped = $absensiQuery->get()->groupBy('id_atlet');

        $lines = [];
        $header = ['Atlet','Pertemuan','Hadir','Sakit','Izin','Alpa','% Hadir'];
        $lines[] = implode(',', array_map(function($v){ return '"'.str_replace('"','""',$v).'"'; }, $header));
        foreach($grouped as $rows){
            $total = $rows->count();
            $hadir = $rows->where('status', 'Hadir')->count();
            $sakit = $rows->where('status', 'Sakit')->count();
            $izin = $rows->where('status', 'Izin')->count();
            $alpa = $rows->where('status', 'Alpa')->count();
            $persen = $total ? round(($hadir/$total)*100,2) : '';
            $row = [
                optional($rows->first()->atlet)->nama,
                $total,
                $hadir,
                $sakit,
                $izin,
                $alpa,
                $persen,
            ];
            $lines[] = implode(',', array_map(function($v){ return '"'.str_replace('"','""',(string)$v).'"'; }, $row));
        }
        $csv = implode("\r\n", $lines);
        $filename = 'rekap_absensi_pelatih_'.str_replace('-', '', $start).'_'.str_replace('-', '', $end).'.csv';
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function isiAbsensi(){
        $pelatihUser = Auth::guard('pelatih')->user();
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            $dataAtlet = Atlet::where('id_cabor', $pelatihUser->id_cabor)->get();
            $dataJadwal = Jadwal::where('id_cabor', $pelatihUser->id_cabor)->get();
        } else {
            $dataAtlet = Atlet::all();
            $dataJadwal = Jadwal::all();
        }
        return view('insert.absensi', compact('dataAtlet', 'dataJadwal'));
    }

    public function simpanAbsensi(Request $request){
        $pelatihUser = Auth::guard('pelatih')->user();
        // Jika ada pengiriman batch (rows[])
        if ($request->has('rows') && is_array($request->input('rows'))) {
            $validate = Validator::make($request->all(), [
                'tanggal_absen' => 'required|date',
                'jadwal' => 'required|date_format:H:i',
                'rows' => 'required|array',
            ]);

            if($validate->fails()){
                return redirect()->back()->with('error', 'Data yang anda masukkan tidak valid. Silakan periksa kembali.');
            }

            $tanggal = $request->input('tanggal_absen');
            $jam = $request->input('jadwal');
            $rows = collect($request->input('rows'));

            // Saring hanya yang dipilih atau memiliki status
            $selected = $rows->filter(function($r){
                return (isset($r['include']) && $r['include']) || (!empty($r['status'] ?? null));
            });

            if ($selected->isEmpty()){
                return redirect()->back()->with('error', 'Pilih minimal satu atlet untuk diabsen.');
            }

            $allowedStatus = ['Hadir','Tidak Hadir','Izin','Sakit','Alpa'];

            $created = 0;
            foreach($selected as $atletId => $payload){
                // Validasi atlet dan status per-baris
                if (!Atlet::where('id', $atletId)->exists()) { continue; }
                // pastikan atlet termasuk cabor pelatih jika pelatih punya id_cabor
                if(isset($pelatihUser) && !empty($pelatihUser->id_cabor)){
                    if(!Atlet::where('id', $atletId)->where('id_cabor', $pelatihUser->id_cabor)->exists()){
                        continue; // skip atlet di luar cabor pelatih
                    }
                }
                $status = $payload['status'] ?? null;
                if (!$status || !in_array($status, $allowedStatus)) { continue; }
                $ket = $payload['keterangan'] ?? '';

                $absensi = new Absensi();
                $absensi->id_atlet = $atletId;
                // Catatan: sistem saat ini menggunakan kolom 'jadwal' bertipe jam string
                $absensi->jadwal = $jam;
                $absensi->tanggal_absen = $tanggal;
                $absensi->status = $status;
                $absensi->keterangan = $ket;
                if($absensi->save()){
                    $created++;
                }
            }

            if ($created === 0){
                return redirect()->back()->with('error', 'Tidak ada baris absensi yang valid untuk disimpan.');
            }

            return redirect()->route('absensi.pelatih')->with('success', 'Absensi tersimpan untuk '.$created.' atlet.');
        }

        // Backward compatibility: input tunggal
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
        // pastikan atlet yang dikirim milik cabor pelatih
        if(isset($pelatihUser) && !empty($pelatihUser->id_cabor)){
            if(!Atlet::where('id', $data['atlet_id'])->where('id_cabor', $pelatihUser->id_cabor)->exists()){
                return redirect()->back()->with('error', 'Anda tidak diperbolehkan memasukkan absensi untuk atlet di luar cabang Anda.');
            }
        }
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
        $pelatihUser = Auth::guard('pelatih')->user();
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            // jika absensi terkait atlet di luar cabor pelatih, tolak
            if($dataAbsensi && $dataAbsensi->atlet && $dataAbsensi->atlet->id_cabor != $pelatihUser->id_cabor){
                return redirect()->back()->with('error', 'Anda tidak diperbolehkan mengubah absensi untuk atlet di luar cabang Anda.');
            }
            $dataAtlet = Atlet::where('id_cabor', $pelatihUser->id_cabor)->get();
        } else {
            $dataAtlet = Atlet::all();
        }
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
        $pelatihUser = Auth::guard('pelatih')->user();
        $absensi = Absensi::where('id', $data['id_absensi'])->first();
        if(!$absensi){
            return redirect()->route('pelatih.absensi')->with('error', 'Data absensi tidak ditemukan.');
        }
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            // pastikan atlet target milik cabor pelatih
            if(!Atlet::where('id', $data['atlet_id'])->where('id_cabor', $pelatihUser->id_cabor)->exists()){
                return redirect()->route('pelatih.absensi')->with('error', 'Anda tidak diperbolehkan mengganti absensi ke atlet di luar cabang Anda.');
            }
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
        $pelatihUser = Auth::guard('pelatih')->user();
        if($pelatihUser && !empty($pelatihUser->id_cabor)){
            if(!$absensi->atlet || $absensi->atlet->id_cabor != $pelatihUser->id_cabor){
                return redirect()->route('pelatih.absensi')->with('error', 'Anda tidak diperbolehkan menghapus absensi atlet di luar cabang Anda.');
            }
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
        $dataJadwal = Jadwal::where('status_verifikasi', 'approved')->get();
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
