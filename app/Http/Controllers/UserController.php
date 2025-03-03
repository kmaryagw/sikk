<?php

namespace App\Http\Controllers;

use App\Models\Fakultasn;
use App\Models\User;

use App\Models\program_studi;
use App\Models\UnitKerja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

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

            $user = Auth::user();
            
            if ($user instanceof User) {
                $user->status = 1;
                $user->save();
            } else {
                Alert::error('Error', 'Username Tidak Valid!');
                return back();
            }

            Alert::success('Sukses', 'Selamat Datang');
            return redirect()->route('dashboard');
        }

        Alert::error('Error', 'Username atau Password salah!');
        return back();

    }

    public function profile()
    {
        $user = Auth::user();
        // dd($user); // Debugging untuk melihat apakah data user tersedia
        
        return view('pages.profile', [
            'user' => $user,
            'title' => 'Profil Pengguna',
            'type_menu' => 'profile'
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user instanceof User) {
            $user->status = 0;
            $user->save();
        } else {
            return redirect('/')->withErrors(['logout' => 'Tidak ada pengguna yang sedang login.']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }


    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Data User';
        $q = $request->query('q');
        $user = Auth::user();

        $usersQuery = User::where('username', 'like', '%' . $q . '%')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'users.prodi_id')
            ->leftJoin('unit_kerja', 'unit_kerja.unit_id', '=', 'users.unit_id')
            ->leftJoin('fakultasn', 'fakultasn.id_fakultas', '=', 'users.id_fakultas')
            ->orderBy('role', 'asc');

        if ($user->role === 'admin') {
            $usersQuery->where(function($query) use ($user) {
                $query->where('users.role', '<>', 'admin')
                      ->orWhere('users.id_user', $user->id_user);
            });
        }

        $users = $usersQuery->paginate(10)->withQueryString();
        $no = $users->firstItem();

        return view('pages.index-user', [
            'title' => $title,
            'users' => $users,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'user',
        ]);
    }


    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Tambah User';
        $roles = ['admin', 'prodi', 'unit kerja', 'fakultas'];
        $prodis = program_studi::orderBy('nama_prodi')->get();
        $units = UnitKerja::orderBy('unit_nama')->get();
        $fakultasns = Fakultasn::orderBy('nama_fakultas')->get();

        return view('pages.create-user', [
            'title' => $title,
            'roles' => $roles,
            'prodis' => $prodis,
            'fakultasns' => $fakultasns,
            'units' => $units,
            'type_menu' => 'masterdata',
            'sub_menu' => 'user',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'status' => 'required|string|in:0,1',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,prodi,unit kerja,fakultas',
        ]);

        if ($request->role === 'prodi') {
            $rules['prodi_id'] = 'required|exists:program_studi,prodi_id';
            $rules['unit_id'] = 'nullable';
            $rules['id_fakultas'] = 'nullable';
        } elseif ($request->role === 'unit kerja') {
            $rules['unit_id'] = 'required|exists:unit_kerja,id_unit_kerja';
            $rules['prodi_id'] = 'nullable';
            $rules['id_fakultas'] = 'nullable';
        } elseif ($request->role === 'fakultas') {
            $rules['id_fakultas'] = 'required|exists:fakultasn,id_fakultas';
            $rules['prodi_id'] = 'nullable';
            $rules['unit_id'] = 'nullable';
        } elseif ($request->role === 'admin') {
            $rules['prodi_id'] = 'nullable';
            $rules['unit_id'] = 'nullable';
            $rules['id_fakultas'] = 'nullable';
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
        $user->id_fakultas = $request->id_fakultas;
    
        $user->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('user.index');
    }

    public function edit(user $user)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Ubah User';
        $roles = ['admin','prodi','unit kerja', 'fakultas'];
        $prodis = program_studi::orderBy('nama_prodi')->get();
        $units = UnitKerja::orderBy('unit_nama')->get();
        $fakultasns = Fakultasn::orderBy('nama_fakultas')->get();
        return view('pages.edit-user', [
            'title' => $title,
            'prodis' => $prodis,
            'units' => $units,
            'fakultasns' => $fakultasns,
            'roles' => $roles,
            'user' => $user,
            'type_menu' => 'masterdata',
            'sub_menu' => 'user',
        ]);
    }

    public function update(User $user, Request $request)
{
    $request->validate([
        'username' => 'required|string|max:255',
        'status' => 'required|string|in:0,1',
        'role' => 'required|in:admin,prodi,unit kerja,fakultas',
    ]);

    if (User::where('username', $request->username)->where('id_user', '<>', $user->id_user)->exists()) {
        return back()->withErrors(['username' => 'Username sudah terdaftar']);
    }

    $user->username = $request->username;
    $user->status = $request->status;
    $user->role = $request->role;

    if ($request->password) {
        $request->validate([
            'password' => 'string|min:8',
        ]);
        $user->password = Hash::make($request->password);
    }

    if ($request->role === 'prodi') {
        $request->validate([
            'prodi_id' => 'required|exists:program_studi,prodi_id',
        ]);
        $user->prodi_id = $request->prodi_id;
        $user->unit_id = null;
        $user->id_fakultas = null;
    } elseif ($request->role === 'unit kerja') {
        $request->validate([
            'unit_id' => 'required|exists:unit_kerja,unit_id',
        ]);
        $user->unit_id = $request->unit_id;
        $user->prodi_id = null;
        $user->id_fakultas = null;
    } elseif ($request->role === 'fakultas') {
        $request->validate([
            'id_fakultas' => 'required|exists:fakultasn,id_fakultas',
        ]);
        $user->id_fakultas = $request->id_fakultas;
        $user->prodi_id = null;
        $user->unit_id = null;
    } else {
        $user->prodi_id = null;
        $user->unit_id = null;
        $user->id_fakultas = null;
    }

    $user->save();

    Alert::success('Sukses', 'Data Berhasil Diubah');
    return redirect()->route('user.index'); // Admin kembali ke daftar user
}

public function updateProfile(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'username' => 'required|string|max:255|unique:users,username,' . $user->id_user . ',id_user',
        'nama' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
    ]);

    $user->username = $request->username;
    $user->nama = $request->nama;
    $user->email = $request->email;
    $user->save();

    return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = Auth::user();

    // Cek apakah password lama benar
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
    }

    // Update password baru
    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('success', 'Password berhasil diperbarui');
}


    public function destroy(user $user)
    {
        $user->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('user.index');
    }
}