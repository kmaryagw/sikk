<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\FakultasnController;
use App\Http\Controllers\IndikatorKinerjaController;
use App\Http\Controllers\LaporanIkuController;
use App\Http\Controllers\LaporanRenjaController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\MonitoringIKUController;
use App\Http\Controllers\OrganisasiJabatanController;
use App\Http\Controllers\PeriodeMonevController;
use App\Http\Controllers\PeriodeMonitoringController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\RealisasiRenjaController;
use App\Http\Controllers\RenstraController;
use App\Http\Controllers\SettingIKUController;
use App\Http\Controllers\StandarController;
use App\Http\Controllers\SuratKlasifikasiLingkupController;
use App\Http\Controllers\SuratKlasifikasiPerihalController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\TargetCapaianController;
use App\Http\Controllers\UserController;
use App\Models\SuratKlasifikasiLingkup;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('/', [UserController::class, 'loginAction'])->name('login.action');
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
    // Route::get('monitoringiku/{mti_id}/create-detail', [MonitoringIKUController::class, 'createDetail'])->name('monitoringiku.create-detail');
    // Route::post('/monitoringiku/{mti_id}/store-detail', [MonitoringIKUController::class, 'storeDetail'])->name('monitoringiku.store-detail');
    Route::get('monitoringiku/{mti_id}/edit-detail', [MonitoringIKUController::class, 'editDetail'])->name('monitoringiku.edit-detail');
    Route::put('monitoringiku/{mti_id}/update-detail', [MonitoringIKUController::class, 'updateDetail'])->name('monitoringiku.update-detail');
    Route::get('monitoringiku/{mti_id}/show-monitoringiku', [MonitoringIKUController::class, 'show'])->name('monitoringiku.show-monitoringiku');

    // // Rute untuk Setting
    // Route::resource('settingiku', SettingIKUController::class);
    // // Route::resource('settingiku', SettingIKUController::class)->except(['create', 'edit', 'show']);
    // // Route::put('/settingiku/{id_setting}', [SettingIKUController::class, 'update'])->name('settingiku.update');

    //Surat
    Route::resource('organisasijabatan', OrganisasiJabatanController::class);
    // Route::resource('suratfungsi', ....Controller::class);
    Route::resource('suratperihal', SuratKlasifikasiPerihalController::class);
    Route::resource('suratlingkup', SuratKlasifikasiLingkupController::class);
    // Route::resource('surat', ....Controller::class);
    // Route::resource('datanomorsurat', ....Controller::class);
    // Route::resource('menungguvalidasi', ....Controller::class);
    // Route::resource('nomorsurat', ....Controller::class);


    // Rute untuk laporan
    Route::resource('laporan-renja', LaporanRenjaController::class);
    Route::get('/export-excel-renja', [LaporanRenjaController::class, 'exportExcel'])->name('export-excel.renja');
    Route::get('/export-pdf-renja', [LaporanRenjaController::class, 'exportPdf'])->name('export-pdf.renja');

    Route::resource('laporan-iku', LaporanIkuController::class);
    Route::get('/export-excel-iku', [LaporanIkuController::class, 'exportExcel'])->name('export-excel.iku');
    Route::get('/export-pdf-iku', [LaporanIkuController::class, 'exportPdf'])->name('export-pdf.iku');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
});
