<?php

use App\Http\Controllers\IndikatorKinerjaUtamaController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PeriodeMonevController;
use App\Http\Controllers\PeriodeMonitoringController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\RealisasiRenjaController;
use App\Http\Controllers\RenstraController;
use App\Http\Controllers\StandarController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\TargetCapaianController;
use App\Http\Controllers\UserController;
use App\Models\Monitoring;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('/', [UserController::class, 'loginAction'])->name('login.action');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {

    Route::resource('user', UserController::class);
    Route::resource('unit', UnitKerjaController::class);
    Route::resource('prodi', ProdiController::class);
    Route::resource('renstra', RenstraController::class);
    Route::resource('tahun', TahunController::class);
    Route::resource('periodemonev', PeriodeMonevController::class);
    Route::resource('indikatorkinerjautama', IndikatorKinerjaUtamaController::class);
    Route::resource('targetcapaian', TargetCapaianController::class);
    Route::resource('programkerja', ProgramKerjaController::class);
    Route::resource('standar', StandarController::class);

    Route::resource('realisasirenja', RealisasiRenjaController::class);
    Route::get('realisasirenja/{rk_id}/realisasi', [RealisasiRenjaController::class, 'showRealisasi'])->name('realisasirenja.showRealisasi');
    Route::resource('monitoring', MonitoringController::class);
    Route::resource('periode-monitoring', PeriodeMonitoringController::class);


    Route::get('/storage/{filename}', function ($filename) {
        $filePath = storage_path('app/public/dokumen/' . $filename);
        if (!file_exists($filePath)) {
            return abort(404, 'File tidak ditemukan.');
        }
        return response()->file($filePath);
    })->where('filename', '.*');
    

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

// Route::get('/', function () {
//     return view('welcome');
// });
