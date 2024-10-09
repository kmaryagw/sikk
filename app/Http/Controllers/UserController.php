<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login()
    {
        $title = 'Login';
        return view('pages.auth-login', compact('title'));
    }

    public function loginAction(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('pages.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau Password salah!',
        ]);
    }
}