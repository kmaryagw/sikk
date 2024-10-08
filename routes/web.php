<?php

use Illuminate\Support\Facades\Route;

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
