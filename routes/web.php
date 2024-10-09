<?php

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
    Route::get('/dashboard', function () {
        return view('pages.dashboard', ['type_menu' => 'dashboard']);
    })->name('pages.dashboard');
    
    Route::get('/audit-internal', function () {
        return view('pages.form-audit-internal', ['type_menu' => 'audit-internal']);
    });
});

// Route::get('/', function () {
//     return view('welcome');
// });