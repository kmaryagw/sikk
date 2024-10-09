<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::post('login', [UserController::class, 'loginAction'])->name('user.login.action');


Route::get('/auditor', function () {
    return view('pages.auditor', ['type_menu' => 'auditor']);
});

Route::get('/formaudit', function () {
    return view('pages.formaudit', ['type_menu' => 'formaudit']);
});

Route::get('/auth-login', function () {
    return view('pages.auth-login', ['type_menu' => 'auth']);
});

Route::get('/dashboard', function () {
    return view('pages.dashboard', ['type_menu' => 'dashboard']);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('Test',function(){
    return "Testing route";
});

Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('/', [UserController::class, 'loginAction'])->name('login.action');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    // Route::get('/user', function () {
    //     return view('pages.user', ['type_menu' => 'user']);
    // })->name('pages.user');

    Route::resource('user', UserController::class);

    Route::get('/dashboard', function () {
        return view('pages.dashboard', ['type_menu' => 'dashboard']);
    })->name('pages.dashboard');
    
    Route::get('/audit-internal', function () {
        return view('pages.form-audit-internal', ['type_menu' => 'audit-internal']);
    });
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
