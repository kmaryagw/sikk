<?php

namespace App\Http\Controllers;
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

        return view('pages.create-user', [
            'title' => $title,
            'roles' => $roles,  // Kirim array role ke view
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
    
        $customPrefix = 'US';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $id_user = $customPrefix . strtoupper($md5Hash);
    
        $user = new User($request->all());
        $user->id_user = $id_user;
        $user->password = Hash::make($request->password);
    
        $user->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('user.index');
    }

    public function edit(user $user)
    {
        $title = 'Ubah User';
        $levels = ['admin','prodi','unit kerja'];
        return view('pages.edit-user', [
            'title' => $title,
            'levels' => $levels,
            'user' => $user,
            'type_menu' => 'masterdata',
        ]);
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'nama_user' => 'required',
            'alamat' => 'required', 
            'email' => 'required',
            'password' => 'required',
        ]);
    
        if (User::where('email', $request->email)->where('id_user', '<>', $user->id_user)->first()) 
            return back()->withErrors(['email' => 'Email sudah terdaftar']);
    
        $user->nama_user = $request->nama_user;
        $user->email = $request->email;
        $user->alamat = $request->alamat;
        $user->level = $request->level;
        if ($request->password)
            $user->password = Hash::make($request->password);
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