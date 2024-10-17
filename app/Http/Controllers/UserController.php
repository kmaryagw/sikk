<?php

namespace App\Http\Controllers;

use App\Models\program_studi;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

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
            'username' => 'required',
            'password' => 'required',
        ]);
        
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            Alert::success('Sukses', 'Selamat Datang');
            return redirect()->route('pages.dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau Password salah!',
        ]);
    }

    public function index(Request $request)
    {
        $title = 'Data User';
        $q = $request->query('q');
        $users = User::where('username', 'like', '%' . $q . '%')
        ->leftjoin('program_studi', 'program_studi.prodi_id', '=', 'users.prodi_id')
        ->leftjoin('unit_kerja', 'unit_kerja.unit_id', '=', 'users.unit_id')
        ->paginate(10)
        ->withQueryString();
        $no = $users->firstItem();
        
        return view('pages.index-user', [
            'title' => $title,
            'users' => $users,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);
    }

    public function create()
    {
        $title = 'Tambah User';
        $roles = ['admin', 'prodi', 'unit kerja'];
        $prodis = program_studi::orderBy('nama_prodi')->get();
        $units = UnitKerja::orderBy('unit_nama')->get();

        return view('pages.create-user', [
            'title' => $title,
            'roles' => $roles,
            'prodis' => $prodis,
            'units' => $units,
            'type_menu' => 'masterdata',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'status' => 'required|string|max:1',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,prodi,unit kerja',
        ]);

        if ($request->role === 'prodi') {
            $rules['prodi_id'] = 'required|exists:program_studi,prodi_id';
            $rules['unit_id'] = 'nullable';
        } elseif ($request->role === 'unit kerja') {
            $rules['unit_id'] = 'required|exists:unit_kerja,id_unit_kerja';
            $rules['prodi_id'] = 'nullable';
        } elseif ($request->role === 'admin') {
            $rules['prodi_id'] = 'nullable';
            $rules['unit_id'] = 'nullable';
        }
    
        
    
        $customPrefix = 'US';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $id_user = $customPrefix . strtoupper($md5Hash);
    
        $user = new User();
        $user->id_user = $id_user;
        $user->username = $request->username;
        $user->status = $request->status;
        $user->role = $request->role;
        $user->password = Hash::make($request->password);

        $user->prodi_id = $request->prodi_id;
        $user->unit_id = $request->unit_id;
    
        $user->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('user.index');
    }

    public function edit(user $user)
    {
        $title = 'Ubah User';
        $roles = ['admin','prodi','unit kerja'];
        $prodis = program_studi::orderBy('nama_prodi')->get();
        $units = UnitKerja::orderBy('unit_nama')->get();
        return view('pages.edit-user', [
            'title' => $title,
            'prodis' => $prodis,
            'units' => $units,
            'roles' => $roles,
            'user' => $user,
            'type_menu' => 'masterdata',
        ]);
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'status' => 'required|string|max:1',
            'role' => 'required|in:admin,prodi,unit kerja',
        ]);

        // Cek apakah username sudah terdaftar dan bukan milik user yang sama
        if (User::where('username', $request->username)->where('id_user', '<>', $user->id_user)->first()) {
            return back()->withErrors(['username' => 'Username sudah terdaftar']);
        }

        // Update data pengguna
        $user->username = $request->username;
        $user->status = $request->status;
        $user->role = $request->role;

        if ($request->password) {
            $request->validate([
                'password' => 'string|min:8',
            ]);
            $user->password = Hash::make($request->password);
        }

        // Menangani prodi_id dan id_unit_kerja
        if ($request->role === 'prodi') {
            $request->validate([
                'prodi_id' => 'required|exists:program_studi,prodi_id',
            ]);
            $user->prodi_id = $request->prodi_id;
            $user->id_unit_kerja = null; // Kosongkan unit kerja
        } elseif ($request->role === 'unit kerja') {
            $request->validate([
                'unit_id' => 'required|exists:unit_kerja,unit_id',
            ]);
            $user->unit_id = $request->id_unit_kerja;
            $user->prodi_id = null; // Kosongkan prodi
        } else {
            $user->prodi_id = null; // Kosongkan prodi untuk admin
            $user->unit_id = null; // Kosongkan unit kerja untuk admin
        }

        $user->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');
        return redirect()->route('user.index');
    }

    
    public function destroy(user $user)
    {
        $user->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('user.index');
    }
}