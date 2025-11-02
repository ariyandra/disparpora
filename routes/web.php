<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\atletController;
use App\Http\Controllers\autentikasiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\pelatihController;

Route::get('/', [Controller::class, 'index']);
Route::get('/login', [Controller::class, 'Autentikasi'])->name('auntentikasi');
Route::get('/loginPelatih', [autentikasiController::class, 'loginPelatih'])->name('login.pelatih');
Route::get('/loginAtlet', [autentikasiController::class, 'loginAtlet'])->name('login.atlet');

//Admin
Route::post('/login', [autentikasiController::class, 'loginAdmin'])->name('login.admin');
Route::middleware(['auth', 'restrict.user'])->group(function(){
    Route::get('/admin/dashboard', [adminController::class, 'index'])->name('dashboard.pegawai');
    Route::get('/admin/dataPelatih', [adminController::class, 'pagePelatih'])->name('data.pelatih');
    Route::get('/admin/logout', [autentikasiController::class, 'logoutAdmin'])->name('logout.admin');
    Route::get('/admin/tambahPelatih', [adminController::class, 'tambahPelatih'])->name('tambah.pelatih');
    Route::post('/admin/simpanPelatihBaru', [adminController::class, 'simpanPelatihBaru'])->name('simpan.pelatihBaru');
    Route::post('/admin/hapusPelatih', [adminController::class, 'hapusPelatih'])->name('hapus.pelatih');
    Route::post('/admin/updatePelatih', [adminController::class, 'updatePelatih'])->name('update.pelatih');
    Route::post('/admin/simpanUpdatePelatih', [adminController::class, 'simpanUpdatePelatih'])->name('simpan.update.pelatih');
    Route::get('/admin/dataAtlet', [adminController::class, 'dataAtlet'])->name('data.atlet');
    Route::get('/admin/tambahAtlet', [adminController::class, 'tambahAtlet'])->name('tambah.atlet');
    Route::post('/admin/simpanAtletBaru', [adminController::class, 'simpanAtletBaru'])->name('simpan.atletBaru');
    Route::post('/admin/hapusAtlet', [adminController::class, 'hapusAtlet'])->name('hapus.atlet');
    Route::post('/admin/updateAtlet', [adminController::class, 'updateAtlet'])->name('update.atlet');
    Route::post('/admin/simpanUpdateAtlet', [adminController::class, 'simpanUpdateAtlet'])->name('simpan.update.atlet');
    Route::get('/admin/cabor', [adminController::class, 'cabor'])->name('cabor');
    Route::get('/admin/caborBaru', [adminController::class, 'caborBaru'])->name('cabor.baru');
    Route::post('/admin/simpanCaborBaru', [adminController::class, 'simpanCaborBaru'])->name('simpan.caborBaru');
    Route::post('/admin/hapusCabor', [adminController::class, 'hapusCabor'])->name('hapus.cabor');
    Route::post('/admin/ubahCabor', [adminController::class, 'ubahCabor'])->name('ubah.cabor');
    Route::post('/admin/simpanUbahCabor', [adminController::class, 'simpanUbahCabor'])->name('simpan.ubah.cabor');
    Route::get('/admin/lapangan', [adminController::class, 'lapangan'])->name('lapangan');
    Route::get('/admin/lapanganBaru', [adminController::class, 'lapanganBaru'])->name('lapangan.baru');
    Route::post('/admin/simpanLapanganBaru', [adminController::class, 'simpanLapanganBaru'])->name('simpan.lapanganBaru');
    Route::post('/admin/ubahLapangan', action: [adminController::class, 'updateDataLapangan'])->name('ubah.lapangan');
    Route::post('/admin/simpanUbahLapangan', [adminController::class, 'simpanUbahLapangan'])->name('simpan.ubah.lapangan');
    Route::post('/admin/hapusLapangan', [adminController::class, 'hapusLapangan'])->name('hapus.lapangan');
    Route::get('/admin/asesmen', [adminController::class, 'asesmen'])->name('asesmen');
    Route::get('/admin/absensi', [adminController::class, 'absensi'])->name('absensi');
    Route::get('/admin/jadwal', [adminController::class, 'jadwal'])->name('jadwal');
    Route::get('/admin/jadwalBaru', [adminController::class, 'jadwalBaru'])->name('jadwal.baru');
    Route::post('/admin/simpanJadwalBaru', [adminController::class, 'simpanJadwal'])->name('simpan.jadwal.baru');
    Route::post('/admin/ubahJadwal', [adminController::class, 'ubahJadwal'])->name('ubah.jadwal');
    Route::post('/admin/simpanUbahJadwal', [adminController::class, 'simpanUbahJadwal'])->name('simpan.ubah.jadwal');
    Route::post('/admin/hapusJadwal', [adminController::class, 'hapusJadwal'])->name('hapus.jadwal');
    Route::get('/admin/user', [adminController::class, 'user'])->name('user');
    Route::get('/admin/tambahUser', [adminController::class, 'tambahUser'])->name('tambahUser');
    Route::post('/admin/simpanUserBaru', [adminController::class, 'simpanUser'])->name('simpanUser');
    Route::post('/admin/ubahDataUser', [adminController::class, 'ubahUser'])->name('ubahDataUser');
    Route::post('/admin/simpanUbahUser', [adminController::class, 'simpanUbahUser'])->name('simpanUbahUser');
    Route::post('/admin/hapusUser', [adminController::class, 'hapusUser'])->name('hapusUser');
    Route::post('/admin/notifikasi', [adminController::class, 'notifikasi'])->name('admin.notifikasi');
    Route::get('/admin/importAtlet', [adminController::class, 'importAtletForm'])->name('import.atlet.form');
    Route::post('/admin/importAtlet', [adminController::class, 'importAtletSubmit'])->name('import.atlet.submit');
    Route::get('/admin/importPelatih', [adminController::class, 'importPelatihForm'])->name('import.pelatih.form');
    Route::post('/admin/importPelatih', [adminController::class, 'importPelatihSubmit'])->name('import.pelatih.submit');
});

//Pelatih
Route::post('/pelatih/login', [autentikasiController::class, 'submitLoginPelatih'])->name('login.pelatih.submit');
Route::middleware('pelatih')->group(function(){
    Route::get('/pelatih/dashboard', [pelatihController::class, 'index'])->name('dashboard.pelatih');
    Route::get('/pelatih/logout', [autentikasiController::class, 'logoutPelatih'])->name('logout.pelatih');
    Route::get('/pelatih/atlet', [pelatihController::class, 'atlet'])->name('atlet.pelatih');
    Route::get('/pelatih/cabor', [pelatihController::class, 'cabor'])->name('cabor.pelatih');
    Route::get('/pelatih/lapangan', [pelatihController::class, 'lapangan'])->name('lapangan.pelatih');
    Route::get('/pelatih/asesmen', [pelatihController::class, 'asesmen'])->name('asesmen.pelatih');
    Route::get('/pelatih/tambahAsesmen', [pelatihController::class, 'tambahAsesmen'])->name('tambah.asesmen');
    Route::post('/pelatih/simpanAsesmen', [pelatihController::class, 'simpanAsesmen'])->name('simpan.asesmen');
    Route::get('/pelatih/absensi', [pelatihController::class, 'absensi'])->name('absensi.pelatih');
    Route::get('/pelatih/isiAbsensi', [pelatihController::class, 'isiAbsensi'])->name('pelatih.isiAbsensi');
    Route::post('/pelatih/simpanAbsensi', [pelatihController::class, 'simpanAbsensi'])->name('pelatih.simpanAbsensi');
    Route::get('/pelatih/ubahAbsensi', [pelatihController::class, 'ubahAbsensi'])->name('pelatih.ubahAbsensi');
    Route::post('/pelatih/simpanUbahAbsensi', [pelatihController::class, 'simpanUbahAbsensi'])->name('pelatih.simpanUbahAbsensi');
    Route::post('/pelatih/absensi/hapus', [pelatihController::class, 'hapusAbsensi'])->name('pelatih.hapusAbsensi');
    Route::get('/pelatih/jadwal', [pelatihController::class, 'jadwal'])->name('jadwal.pelatih');
    Route::post('/pelatih/notifikasi', [pelatihController::class, 'notifikasi'])->name('pelatih.notifikasi');
});

//Atlet
Route::post('/atlet/login', [autentikasiController::class, 'submitLoginAtlet'])->name('login.atlet.submit');
Route::middleware('atlet')->group(function() {
    Route::get('/atlet/dashboard', [atletController::class, 'index'])->name('dashboard.atlet');
    Route::get('/atlet/logout', [autentikasiController::class, 'logoutAtlet'])->name('logout.atlet');
    Route::get('/atlet/editBiodata', [atletController::class, 'editBiodata'])->name('atlet.editBiodata');
    Route::post('/atlet/simpanUpdateBiodata', [atletController::class, 'simpanUpdateBiodata'])->name('atlet.simpanUpdateBiodata');
    Route::get('/atlet/cabor', [atletController::class, 'cabor'])->name('atlet.cabor');
    Route::get('/atlet/lapangan', [atletController::class, 'lapangan'])->name('atlet.lapangan');
    Route::get('/atlet/asesmen', [atletController::class, 'asesmen'])->name('atlet.asesmen');
    Route::get('/atlet/absensi', [atletController::class, 'absensi'])->name('atlet.absensi');
    Route::get('/atlet/jadwal', [atletController::class, 'jadwal'])->name('atlet.jadwal');
    Route::post('/atlet/notifikasi', [atletController::class, 'notifikasi'])->name('atlet.notifikasi');
});


