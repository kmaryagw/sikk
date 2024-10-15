<?php

use App\Http\Controllers\PeriodeMonevController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RenstraController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('/', [UserController::class, 'loginAction'])->name('login.action');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {

    Route::resource('user', UserController::class);
    Route::resource('unit', UnitKerjaController::class);
    Route::resource('program-studi', ProdiController::class);
    Route::resource('rencana-strategis', RenstraController::class);
    Route::resource('tahun', TahunController::class);
    Route::resource('periode-monev', PeriodeMonevController::class);
    

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
