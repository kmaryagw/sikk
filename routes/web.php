<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataNomorSuratController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\FakultasnController;
use App\Http\Controllers\IndikatorKinerjaController;
use App\Http\Controllers\LaporanIkuController;
use App\Http\Controllers\LaporanRenjaController;
use App\Http\Controllers\MenungguValidasiController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\MonitoringIKUController;
use App\Http\Controllers\NomorSuratController;
use App\Http\Controllers\OrganisasiJabatanController;
use App\Http\Controllers\PeriodeMonevController;
use App\Http\Controllers\PeriodeMonitoringController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\RealisasiRenjaController;
use App\Http\Controllers\RenstraController;
use App\Http\Controllers\SettingIKUController;
use App\Http\Controllers\StandarController;
use App\Http\Controllers\SuratKlasifikasiFungsiController;
use App\Http\Controllers\SuratKlasifikasiLingkupController;
use App\Http\Controllers\SuratKlasifikasiPerihalController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\TargetCapaianController;
use App\Http\Controllers\TargetCapaianProdiController;
use App\Http\Controllers\UserController;
use App\Models\SuratKlasifikasiFungsi;
use App\Models\SuratKlasifikasiLingkup;
use App\Http\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Route;


// Halaman publik (/) → tampilkan index-announcement.blade.php
Route::get('/', [AnnouncementController::class, 'publicPage'])->name('announcement.public');

// Admin (CRUD) → tampilkan index-admin-announcement.blade.php
Route::middleware('auth')->group(function () {
    Route::resource('announcement', AnnouncementController::class);
});


// Route login dipindah ke misalnya /login
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'loginAction'])->name('login.action');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    
    // Resource routes untuk berbagai entitas
    Route::resource('user', UserController::class);
    Route::resource('unit', UnitKerjaController::class);
    Route::resource('prodi', ProdiController::class);
    Route::resource('fakultasn', FakultasnController::class);
    Route::resource('renstra', RenstraController::class);
    Route::resource('tahun', TahunController::class);
    Route::resource('periodemonev', PeriodeMonevController::class);
    Route::resource('indikatorkinerja', IndikatorKinerjaController::class)->except(['show']);
    Route::resource('targetcapaian', TargetCapaianController::class);
    Route::resource('programkerja', ProgramKerjaController::class);
    Route::resource('standar', StandarController::class);

    Route::resource('targetcapaianprodi', TargetCapaianProdiController::class);


    // Route untuk Profil
    Route::get('/profile', [UserController::class, 'profile'])->name('profile')->middleware('auth');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/update-password', [UserController::class, 'updatePassword'])->name('profile.update-password');
    
    Route::get('/indikatorkinerja/template', [IndikatorKinerjaController::class, 'downloadTemplate'])->name('indikatorkinerja.template');
    Route::post('/indikatorkinerja/import', [IndikatorKinerjaController::class, 'import'])->name('indikatorkinerja.import');


    Route::resource('realisasirenja', RealisasiRenjaController::class);
    Route::get('realisasirenja/{rk_id}/realisasi', [RealisasiRenjaController::class, 'showRealisasi'])->name('realisasirenja.showRealisasi');
    
    // Route untuk Monitoring
    Route::get('/monitoring/{pmo_id}', [MonitoringController::class, 'show'])->name('monitoring.view');
    Route::get('/monitoring/{pmo_id}/fill', [MonitoringController::class, 'fill'])->name('monitoring.fill');
    Route::post('/monitoring/{pmo_id}/store', [MonitoringController::class, 'store'])->name('monitoring.fillStore');
    Route::get('monitoring/{pmo_id}/{rk_id}/getData', [MonitoringController::class, 'getData'])->name('monitoring.getData');
    Route::get('/realisasi/create', [RealisasiRenjaController::class, 'create'])->name('realisasi.create');
    
    // Rute untuk Evaluasi
    Route::resource('monitoring', MonitoringController::class);
    Route::resource('periode-monitoring', PeriodeMonitoringController::class);
    Route::resource('monitoringiku', MonitoringIKUController::class);

    Route::post('/monitoringiku/final/{id}', [MonitoringIKUController::class, 'final'])->name('monitoringiku.final');
    Route::get('monitoringiku/{mti_id}/index-detail', [MonitoringIKUController::class, 'indexDetail'])->name('monitoringiku.index-detail');
    Route::get('monitoringiku/{mti_id}/create-detail', [MonitoringIKUController::class, 'createDetail'])->name('monitoringiku.create-detail');
    Route::post('monitoringiku/{mti_id}/store-detail', [MonitoringIKUController::class, 'storeDetail'])->name('monitoringiku.store-detail');
    Route::get('monitoringiku/{mti_id}/edit-detail/{ti_id}', [MonitoringIKUController::class, 'editDetail'])->name('monitoringiku.edit-detail');
    Route::put('monitoringiku/{mti_id}/update-detail/{ti_id}', [MonitoringIKUController::class, 'updateDetail'])->name('monitoringiku.update-detail');
    Route::get('monitoringiku/{mti_id}/show-monitoringiku', [MonitoringIKUController::class, 'show'])->name('monitoringiku.show-monitoringiku');

    // // Rute untuk Setting
    // Route::resource('settingiku', SettingIKUController::class);
    // // Route::resource('settingiku', SettingIKUController::class)->except(['create', 'edit', 'show']);
    // // Route::put('/settingiku/{id_setting}', [SettingIKUController::class, 'update'])->name('settingiku.update');

    //MasterSurat
    Route::resource('organisasijabatan', OrganisasiJabatanController::class);
    Route::resource('suratfungsi', SuratKlasifikasiFungsiController::class);
    Route::resource('suratperihal', SuratKlasifikasiPerihalController::class);
    Route::resource('suratlingkup', SuratKlasifikasiLingkupController::class);

    //Surat
    Route::resource('datanomorsurat', DataNomorSuratController::class);
    Route::resource('menungguvalidasi', MenungguValidasiController::class);
    Route::post('/menungguvalidasi/{id}/valid', [MenungguValidasiController::class, 'validateSuratAdmin'])->name('menungguvalidasi.valid');
    Route::post('/menungguvalidasi/{id}/revisi', [MenungguValidasiController::class, 'revisiSurat'])->name('menungguvalidasi.revisi');

    //NomorSurat
    Route::resource('nomorsurat', NomorSuratController::class);
    Route::post('/nomorsurat/{id}/validasi', [NomorSuratController::class, 'validateSurat'])->name('nomorsurat.validasi');
    Route::post('/nomorsurat/{id}/ajukan', [NomorSuratController::class, 'ajukanData'])->name('nomorsurat.ajukan');



    // Rute untuk laporan
    Route::resource('laporan-renja', LaporanRenjaController::class);
    Route::get('/export-excel-renja', [LaporanRenjaController::class, 'exportExcel'])->name('export-excel.renja');
    Route::get('/laporan-renja/export-excel/rsk', [LaporanRenjaController::class, 'exportExcelRsk'])->name('export-excel.renja.rsk');
    Route::get('/laporan-renja/export-excel/informatika', [LaporanRenjaController::class, 'exportExcelIf'])->name('export-excel.renja.if');
    Route::get('/laporan-renja/export-excel/bisnis-digital', [LaporanRenjaController::class, 'exportExcelBd'])->name('export-excel.renja.bd');
    Route::get('/laporan-renja/export-excel/dkv', [LaporanRenjaController::class, 'exportExcelDkv'])->name('export-excel.renja.dkv');

    Route::get('/export-pdf-renja', [LaporanRenjaController::class, 'exportPdf'])->name('export-pdf.renja');
    Route::get('/export/pdf/renja/informatika', [LaporanRenjaController::class, 'exportPdfInformatika'])->name('export-pdf.renja.if');
    Route::get('/export/pdf/renja/rekayasa-sistem-komputer', [LaporanRenjaController::class, 'exportPdfRSK'])->name('export-pdf.renja.rsk');
    Route::get('/export/pdf/renja/bisnis-digital', [LaporanRenjaController::class, 'exportPdfBD'])->name('export-pdf.renja.bd');
    Route::get('/export/pdf/renja/desain-komunikasi-visual', [LaporanRenjaController::class, 'exportPdfDKV'])->name('export-pdf.renja.dkv');


    Route::resource('laporan-iku', LaporanIkuController::class);
    Route::get('/export-excel-iku', [LaporanIkuController::class, 'exportExcel'])->name('export-excel.iku');
    Route::get('/export-excel/iku/informatika', [LaporanIkuController::class, 'exportExcelIF'])->name('export-excel.iku.if');
    Route::get('/export-excel/iku/rsk', [LaporanIkuController::class, 'exportExcelRSK'])->name('export-excel.iku.rsk');
    Route::get('/export-excel/iku/bd', [LaporanIkuController::class, 'exportExcelBD'])->name('export-excel.iku.bd');
    Route::get('/export-excel/iku/dkv', [LaporanIkuController::class, 'exportExcelDKV'])->name('export-excel.iku.dkv');

    Route::get('/export-pdf-iku', [LaporanIkuController::class, 'exportPdf'])->name('export-pdf.iku');
    Route::get('/export-pdf/iku/informatika', [LaporanIKUController::class, 'exportPdfIF'])->name('export-pdf.iku.if');
    Route::get('/export-pdf/iku/rsk', [LaporanIKUController::class, 'exportPdfRSK'])->name('export-pdf.iku.rsk');
    Route::get('/export-pdf/iku/bd', [LaporanIKUController::class, 'exportPdfBD'])->name('export-pdf.iku.bd');
    Route::get('/export-pdf/iku/dkv', [LaporanIKUController::class, 'exportPdfDKV'])->name('export-pdf.iku.dkv');

    Route::get('/monitoringiku/{mti_id}/export/{type}', [MonitoringIKUController::class, 'exportDetail'])
    ->name('monitoringiku.export-detail');



    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
});
