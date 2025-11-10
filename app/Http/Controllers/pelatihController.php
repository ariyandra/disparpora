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
use Carbon\Carbon;

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
        $pelatihUser = Auth::guard('pelatih')->user();
        if($pelatihUser){
            // only show athletes assigned to this pelatih
            $dataAtlet = Atlet::with('documents')->where('status_verifikasi', 'approved')->where('id_pelatih', $pelatihUser->id)->get();
            if($dataAtlet->isEmpty()){
                session()->flash('warning', 'Tidak ada atlet yang terdaftar pada Anda. Halaman menampilkan atlet yang terhubung ke akun Anda.');
            }
        } else {
            $dataAtlet = Atlet::with('documents')->where('status_verifikasi', 'approved')->get();
        }
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

        // Batasi asesmen hanya untuk atlet yang terdaftar pada pelatih ini
        if($pelatihUser){
            $asesmenQuery->whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_pelatih', $pelatihUser->id);
            });
        }

    $asesmenRows = $asesmenQuery->get();

        // If result is empty because pelatih has no assigned athletes, keep empty and flash a warning (strict ownership)
        if($pelatihUser && $asesmenRows->isEmpty()){
            session()->flash('warning', 'Data asesmen untuk atlet Anda tidak ditemukan. Anda hanya dapat melihat asesmen untuk atlet yang terdaftar pada akun Anda.');
            // keep $asesmenRows empty to enforce 1-atlet-1-pelatih ownership
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

        // Batasi daftar atlet hanya yang terkait ke pelatih ini (strict ownership)
        if($pelatihUser){
            $atlets = Atlet::where('id_pelatih', $pelatihUser->id)->get();
            if($atlets->isEmpty()){
                session()->flash('warning', 'Tidak ditemukan atlet yang terdaftar pada Anda. Anda hanya dapat memilih atlet yang terdaftar pada akun Anda.');
                // keep $atlets empty to enforce ownership
            }
        } else {
            $atlets = Atlet::all();
        }

    return view('pelatih.asesmen', compact('dataAsesmen', 'rekap', 'filters', 'atlets'));
    }

    public function tambahAsesmen(){
        $pelatihUser = Auth::guard('pelatih')->user();
        // show only athletes assigned to this pelatih
        if($pelatihUser){
            $dataAtlet = Atlet::where('id_pelatih', $pelatihUser->id)->get();
            if($dataAtlet->isEmpty()){
                // Strict: do NOT fall back to all athletes. Pelatih should only see their own athletes.
                session()->flash('warning', 'Tidak ditemukan atlet yang terdaftar pada Anda. Anda hanya bisa menambahkan asesmen untuk atlet yang terdaftar pada Anda.');
                // keep $dataAtlet as empty collection
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
        // pastikan atlet yang dinilai memang terdaftar pada pelatih ini
        if($pelatihUser){
            if(!Atlet::where('id', $data['nama_atlet'])->where('id_pelatih', $pelatihUser->id)->exists()){
                return redirect()->back()->with('error', 'Anda tidak diperbolehkan menambahkan asesmen untuk atlet yang bukan milik Anda.');
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
        // Batasi tampilan absensi hanya untuk atlet yang terdaftar pada pelatih ini
        if($pelatihUser){
            $dataAbsensi = Absensi::whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_pelatih', $pelatihUser->id);
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
        if(isset($pelatihUser)){
            $absensiQuery->whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_pelatih', $pelatihUser->id);
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
        // Batasi daftar atlet di filter/selector hanya ke atlet milik pelatih
        if(isset($pelatihUser)){
            $atlets = Atlet::where('id_pelatih', $pelatihUser->id)->get();
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
        if($pelatihUser){
            $asesmenQuery->whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_pelatih', $pelatihUser->id);
            });
        }
        $asesmenRows = $asesmenQuery->get();
        // If pelatih has no assigned athletes, do not fall back to showing all asesmen — enforce ownership
        if($pelatihUser && $asesmenRows->isEmpty()){
            session()->flash('warning', 'Data asesmen untuk atlet Anda tidak ditemukan. Hanya asesmen untuk atlet yang terdaftar pada akun Anda akan ditampilkan.');
            // keep $asesmenRows empty to enforce 1-atlet-1-pelatih
        }
        $asesmenRows = $asesmenRows->groupBy('id_atlet');

    $lines = [];
    // Use semicolon delimiter and add title rows for Excel on Windows
    $delimiter = ';';
    $lines[] = '"DISPARPORA"';
    $lines[] = '"rekapitulasi asesmen"';
    $header = ['Atlet','Jumlah','Rata2 Fisik','Rata2 Teknik','Rata2 Sikap','Terakhir Asesmen'];
    $lines[] = implode($delimiter, array_map(function($v){ return '"'.str_replace('"','""',$v).'"'; }, $header));
        foreach($asesmenRows as $rows){
            $row = [
                optional($rows->first()->atlet)->nama,
                $rows->count(),
                round($rows->avg('aspek_fisik'),2),
                round($rows->avg('aspek_teknik'),2),
                round($rows->avg('aspek_sikap'),2),
                $rows->sortByDesc('tanggal_asesmen')->first()->tanggal_asesmen,
            ];
            $lines[] = implode($delimiter, array_map(function($v){ return '"'.str_replace('"','""',(string)$v).'"'; }, $row));
        }
        // Prepend UTF-8 BOM so Excel recognizes UTF-8 encoding
        $csv = "\xEF\xBB\xBF" . implode("\r\n", $lines);
        $filename = 'rekap_asesmen_pelatih_'.str_replace('-', '', $start).'_'.str_replace('-', '', $end).'.csv';
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
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
    // Batasi export ke atlet pelatih jika pelatih punya atlet (ownership-based filtering)
        if($pelatihUser){
            $absensiQuery->whereHas('atlet', function($q) use($pelatihUser){
                $q->where('id_pelatih', $pelatihUser->id);
            });
        }
        $grouped = $absensiQuery->get()->groupBy('id_atlet');

        $lines = [];
    // Use semicolon as delimiter for CSV to better match Excel's locale on Windows
    $delimiter = ';';
    // Add top title rows as requested so the CSV shows a header block
    $lines[] = '"DISPARPORA"';
    $lines[] = '"rekapitulasi absensi"';
    $header = ['Atlet','Pertemuan','Hadir','Sakit','Izin','Alpa','%Hadir'];
        $lines[] = implode($delimiter, array_map(function($v){ return '"'.str_replace('"','""',$v).'"'; }, $header));
        foreach($grouped as $rows){
            $total = $rows->count();
            $hadir = $rows->where('status', 'Hadir')->count();
            $sakit = $rows->where('status', 'Sakit')->count();
            $izin = $rows->where('status', 'Izin')->count();
            $alpa = $rows->where('status', 'Alpa')->count();
            // For CSV export show percentage as integer (no trailing '%') to match spreadsheet view
            $persen = $total ? round(($hadir/$total)*100, 0) : '';
            $row = [
                optional($rows->first()->atlet)->nama,
                $total,
                $hadir,
                $sakit,
                $izin,
                $alpa,
                $persen,
            ];
            $lines[] = implode($delimiter, array_map(function($v){ return '"'.str_replace('"','""',(string)$v).'"'; }, $row));
        }
        // Prepend UTF-8 BOM so Excel on Windows recognizes UTF-8 and splits columns correctly
        $csv = "\xEF\xBB\xBF" . implode("\r\n", $lines);
        $filename = 'rekap_absensi_pelatih_'.str_replace('-', '', $start).'_'.str_replace('-', '', $end).'.csv';
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function isiAbsensi(){
        $pelatihUser = Auth::guard('pelatih')->user();
        // show only athletes assigned to this pelatih
        if($pelatihUser){
            $dataAtlet = Atlet::where('id_pelatih', $pelatihUser->id)->get();
            if($dataAtlet->isEmpty()){
                // Strict policy: do NOT fall back to all athletes. Pelatih may only operate on assigned athletes.
                session()->flash('warning', 'Tidak ditemukan atlet yang terdaftar pada Anda. Anda hanya bisa memasukkan absensi untuk atlet yang terdaftar pada Anda.');
                // leave $dataAtlet as empty collection
            }
                // keep jadwal limited to cabors of athletes assigned to this pelatih if any
                // (compute from assigned athletes so pelatih with multiple cabors sees only relevant jadwal)
                // use distinct DB query to avoid complex collection chaining that may fail on older environments
                $caborIds = Atlet::where('id_pelatih', $pelatihUser->id)
                    ->whereNotNull('id_cabor')
                    ->distinct()
                    ->pluck('id_cabor')
                    ->toArray();
            if (!empty($caborIds)) {
                $dataJadwal = Jadwal::whereIn('id_cabor', $caborIds)->get();
            } elseif (!empty($pelatihUser->id_cabor)) {
                // legacy fallback: if pelatih has a single id_cabor set on profile
                $dataJadwal = Jadwal::where('id_cabor', $pelatihUser->id_cabor)->get();
            } else {
                // strict: no assigned cabors and no profile cabor -> no jadwal to choose
                $dataJadwal = collect();
            }
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
            // Basic presence check; we'll parse jadwal flexibly (either datetime-local or date+time)
            $validate = Validator::make($request->all(), [
                'rows' => 'required|array',
                'jadwal' => 'required|string',
            ]);

            if($validate->fails()){
                return redirect()->back()->with('error', 'Data yang anda masukkan tidak valid. Silakan periksa kembali.');
            }

            $rawJadwal = $request->input('jadwal');
            $tanggal = $request->input('tanggal_absen'); // may be present for backward compatibility
            $rows = collect($request->input('rows'));

            // Saring hanya yang dipilih atau memiliki status
            $selected = $rows->filter(function($r){
                return (!empty($r['status'] ?? null));
            });

            if ($selected->isEmpty()){
                return redirect()->back()->with('error', 'Pilih minimal satu atlet untuk diabsen.');
            }

            $allowedStatus = ['Hadir','Tidak Hadir','Izin','Sakit','Alpa'];

            // Normalise jadwal input to Y-m-d H:i:s (accept either datetime-local like 2025-11-10T14:30 or time-only H:i with tanggal_absen)
            $jadwal_dt = null;
            try {
                if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $rawJadwal)) {
                    $jadwal_dt = Carbon::createFromFormat('Y-m-d\TH:i', $rawJadwal)->format('Y-m-d H:i:s');
                } elseif (!empty($tanggal) && preg_match('/^\d{2}:\d{2}$/', $rawJadwal)) {
                    $jadwal_dt = Carbon::parse($tanggal . ' ' . $rawJadwal)->format('Y-m-d H:i:s');
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:?\d{0,2}$/', $rawJadwal)) {
                    $jadwal_dt = Carbon::parse($rawJadwal)->format('Y-m-d H:i:s');
                }
            } catch (\Exception $e) {
                $jadwal_dt = null;
            }

            if (!$jadwal_dt) {
                return redirect()->back()->with('error', 'Format jadwal tidak dikenali. Gunakan datetime (YYYY-MM-DDTHH:MM) atau pilih tanggal + jam.');
            }

            $created = 0;
            foreach($selected as $atletId => $payload){
                // Validasi atlet dan status per-baris
                if (!Atlet::where('id', $atletId)->exists()) { continue; }
                // pastikan atlet termasuk atlet milik pelatih ini
                if(isset($pelatihUser)){
                    if(!Atlet::where('id', $atletId)->where('id_pelatih', $pelatihUser->id)->exists()){
                        continue; // skip atlet yang bukan milik pelatih ini
                    }
                }
                $status = $payload['status'] ?? null;
                if (!$status || !in_array($status, $allowedStatus)) { continue; }
                $ket = $payload['keterangan'] ?? '';

                $absensi = new Absensi();
                $absensi->id_atlet = $atletId;
                // store both new datetime and keep tanggal_absen for compatibility
                $absensi->jadwal_datetime = $jadwal_dt;
                    if (!empty($tanggal)) {
                    // allow model casting to handle string/date
                    $absensi->tanggal_absen = $tanggal;
                } else {
                    // fallback to date portion of jadwal — store as Carbon instance so casting is consistent
                    $absensi->tanggal_absen = Carbon::parse($jadwal_dt);
                }
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
            'nama_atlet' => 'nullable|string|max:255',
            'atlet_id' => 'required|exists:atlets,id',
            'jadwal' => 'required|string',
            'status' => 'required|string|max:15',
            'tanggal_absen' => 'nullable|date',
            'keterangan' => 'required|string',
        ]);

        if($validate->fails()){
            return redirect()->back()->with('error', 'Data yang anda masukkan tidak valid. Silakan periksa kembali.');
        }

        $data = $validate->validated();
        // pastikan atlet yang dikirim memang terdaftar pada pelatih ini
        if(isset($pelatihUser)){
            if(!Atlet::where('id', $data['atlet_id'])->where('id_pelatih', $pelatihUser->id)->exists()){
                return redirect()->back()->with('error', 'Anda tidak diperbolehkan memasukkan absensi untuk atlet yang bukan milik Anda.');
            }
        }
        // Normalize jadwal input: accept datetime-local (Y-m-d\TH:i) or fall back to tanggal_absen + H:i
        $rawJadwal = $data['jadwal'];
        $jadwal_dt = null;
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $rawJadwal)) {
                $jadwal_dt = Carbon::createFromFormat('Y-m-d\TH:i', $rawJadwal)->format('Y-m-d H:i:s');
            } elseif (!empty($data['tanggal_absen']) && preg_match('/^\d{2}:\d{2}$/', $rawJadwal)) {
                $jadwal_dt = Carbon::parse($data['tanggal_absen'] . ' ' . $rawJadwal)->format('Y-m-d H:i:s');
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:?\d{0,2}$/', $rawJadwal)) {
                $jadwal_dt = Carbon::parse($rawJadwal)->format('Y-m-d H:i:s');
            }
        } catch (\Exception $e) {
            $jadwal_dt = null;
        }

        if (!$jadwal_dt) {
            return redirect()->back()->with('error', 'Format jadwal tidak dikenali. Gunakan datetime (YYYY-MM-DDTHH:MM) atau pilih tanggal + jam.');
        }

        $absensi = new Absensi();
        $absensi->id_atlet = $data['atlet_id'];
    $absensi->jadwal_datetime = $jadwal_dt;
    $absensi->tanggal_absen = !empty($data['tanggal_absen']) ? $data['tanggal_absen'] : Carbon::parse($jadwal_dt);
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
        if($pelatihUser){
            // jika absensi terkait atlet yang bukan milik pelatih, tolak
            if($dataAbsensi && $dataAbsensi->atlet && $dataAbsensi->atlet->id_pelatih != $pelatihUser->id){
                return redirect()->back()->with('error', 'Anda tidak diperbolehkan mengubah absensi untuk atlet yang bukan milik Anda.');
            }
            $dataAtlet = Atlet::where('id_pelatih', $pelatihUser->id)->get();
        } else {
            $dataAtlet = Atlet::all();
        }
        return view('insert.ubahAbsensi', compact( 'dataAbsensi', 'dataAtlet'));
    }

    public function simpanUbahAbsensi(Request $request){
        $validate = Validator::make($request->all(), [
            'id_absensi' => 'required|exists:absensis,id',
            'atlet_id' => 'required|exists:atlets,id',
            'jadwal' => 'required|string',
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
        if($pelatihUser){
            // pastikan atlet target milik pelatih ini
            if(!Atlet::where('id', $data['atlet_id'])->where('id_pelatih', $pelatihUser->id)->exists()){
                return redirect()->route('pelatih.absensi')->with('error', 'Anda tidak diperbolehkan mengganti absensi ke atlet yang bukan milik Anda.');
            }
        }

        // Normalize jadwal and store in jadwal_datetime
        $rawJadwal = $data['jadwal'];
        $jadwal_dt = null;
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $rawJadwal)) {
                $jadwal_dt = Carbon::createFromFormat('Y-m-d\TH:i', $rawJadwal)->format('Y-m-d H:i:s');
            } elseif (preg_match('/^\d{2}:\d{2}$/', $rawJadwal)) {
                $jadwal_dt = Carbon::parse($data['tanggal_absen'] . ' ' . $rawJadwal)->format('Y-m-d H:i:s');
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:?\d{0,2}$/', $rawJadwal)) {
                $jadwal_dt = Carbon::parse($rawJadwal)->format('Y-m-d H:i:s');
            }
        } catch (\Exception $e) {
            $jadwal_dt = null;
        }

        if (!$jadwal_dt) {
            return redirect()->route('absensi.pelatih')->with('error', 'Format jadwal tidak dikenali saat mengubah absensi.');
        }

        $absensi->id_atlet = $data['atlet_id'];
        $absensi->jadwal_datetime = $jadwal_dt;
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
        if($pelatihUser){
            if(!$absensi->atlet || $absensi->atlet->id_pelatih != $pelatihUser->id){
                return redirect()->route('pelatih.absensi')->with('error', 'Anda tidak diperbolehkan menghapus absensi atlet yang bukan milik Anda.');
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
