<?php

use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\FalkutasController;
use App\Http\Controllers\FalkutasnController;
use App\Http\Controllers\IndikatorKinerjaController;
use App\Http\Controllers\LaporanIkuController;
use App\Http\Controllers\LaporanRenjaController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PeriodeMonevController;
use App\Http\Controllers\PeriodeMonitoringController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\RealisasiRenjaController;
use App\Http\Controllers\RenstraController;
use App\Http\Controllers\SettingIKUController;
use App\Http\Controllers\StandarController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\TargetCapaianController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('/', [UserController::class, 'loginAction'])->name('login.action');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    
    // Resource routes untuk berbagai entitas
    Route::resource('user', UserController::class);
    Route::resource('unit', UnitKerjaController::class);
    Route::resource('prodi', ProdiController::class);
    Route::resource('falkutasn', FalkutasnController::class);
    Route::resource('renstra', RenstraController::class);
    Route::resource('tahun', TahunController::class);
    Route::resource('periodemonev', PeriodeMonevController::class);
    Route::resource('indikatorkinerja', IndikatorKinerjaController::class);
    Route::resource('targetcapaian', TargetCapaianController::class);
    Route::resource('programkerja', ProgramKerjaController::class);
    Route::resource('standar', StandarController::class);

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
    Route::resource('evaluasi', EvaluasiController::class);

    Route::post('/evaluasi/final/{id}', [EvaluasiController::class, 'final'])->name('evaluasi.final');
    Route::get('evaluasi/{eval_id}/index-detail', [EvaluasiController::class, 'indexDetail'])->name('evaluasi.index-detail');
    // Route::get('evaluasi/{eval_id}/create-detail', [EvaluasiController::class, 'createDetail'])->name('evaluasi.create-detail');
    // Route::post('/evaluasi/{eval_id}/store-detail', [EvaluasiController::class, 'storeDetail'])->name('evaluasi.store-detail');
    Route::get('evaluasi/{eval_id}/edit-detail', [EvaluasiController::class, 'editDetail'])->name('evaluasi.edit-detail');
    Route::put('evaluasi/{eval_id}/update-detail', [EvaluasiController::class, 'updateDetail'])->name('evaluasi.update-detail');
    Route::delete('evaluasi/destroy-detail/{eval_id}', [EvaluasiController::class, 'destroyDetail'])->name('evaluasi.destroy-detail');
    Route::get('evaluasi/{eval_id}/show-evaluasi', [EvaluasiController::class, 'show'])->name('evaluasi.show-evaluasi');

    // Rute untuk Setting
    Route::resource('settingiku', SettingIKUController::class)->except(['create', 'edit', 'show']);
    Route::put('/settingiku/{id_setting}', [SettingIKUController::class, 'update'])->name('settingiku.update');


    // Rute untuk laporan
    Route::resource('laporan-renja', LaporanRenjaController::class);
    Route::get('/export-excel-renja', [LaporanRenjaController::class, 'exportExcel'])->name('export-excel.renja');
    Route::get('/export-pdf-renja', [LaporanRenjaController::class, 'exportPdf'])->name('export-pdf.renja');

    Route::resource('laporan-iku', LaporanIkuController::class);
    Route::get('/export-excel-iku', [LaporanIkuController::class, 'exportExcel'])->name('export-excel.iku');
    Route::get('/export-pdf-iku', [LaporanIkuController::class, 'exportPdf'])->name('export-pdf.iku');

    // Routes untuk Akses File
    Route::get('/storage/{filename}', function ($filename) {
        $filePath = storage_path('app/public/dokumen/' . $filename);
        if (!file_exists($filePath)) {
            return abort(404, 'File tidak ditemukan.');
        }
        return response()->file($filePath);
    })->where('filename', '.*');
    
    Route::get('/realisasi_files/{filename}', function ($filename) {
        $filePath = storage_path('app/public/realisasi_files/' . $filename);
        if (!file_exists($filePath)) {
            return abort(404, 'File tidak ditemukan.');
        }
        return response()->file($filePath);
    })->where('filename', '.*');    

    // Dashboard & Audit Forms
    Route::get('/dashboard', function () {
        return view('pages.dashboard', ['type_menu' => 'dashboard']);
    })->name('pages.dashboard');
    
    Route::get('/auditor', function () {
        return view('pages.auditor', ['type_menu' => 'auditor']);
    });
    
    Route::get('/formaudit', function () {
        return view('pages.formaudit', ['type_menu' => 'formaudit']);
    });
});

// Route untuk menyimpan Realisasi Renja
// Route::post('/realisasirenja/store', [RealisasiRenjaController::class, 'store'])->name('realisasirenja.store');
