<?php

namespace App\Http\Controllers;

use App\Models\Fakultasn;
use App\Models\program_studi;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProdiController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();

            if ($user->role !== 'admin') {
                abort(403, 'Unauthorized action.');
            }
    
        $title = 'Data Prodi';
        $q = $request->query('q');
        $prodis = program_studi::with('Fakultasn')
            ->where('nama_prodi', 'like', '%' . $q . '%')
            ->orderBy('nama_prodi', 'asc')
            ->paginate(10)
            ->withQueryString();
        $no = $prodis->firstItem();
    
        return view('pages.index-prodi', [
            'title' => $title,
            'prodis' => $prodis,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'prodi',
        ]);
    }


    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $title = 'Tambah Prodi';
        $fakultas = Fakultasn::all();

        return view('pages.create-prodi', [
            'title' => $title,
            'fakultas' => $fakultas,
            'type_menu' => 'masterdata',
            'sub_menu' => 'prodi',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_prodi' => 'required|string|max:255',
            'id_fakultas' => 'required|string|max:50',
            'singkatan_prodi' => 'required|string|max:10',
        ]);

        $customPrefix = 'PR';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $prodi_id = $customPrefix . strtoupper($md5Hash);

        $program_studi = new program_studi();
        $program_studi->prodi_id = $prodi_id;
        $program_studi->nama_prodi = $request->nama_prodi;
        $program_studi->singkatan_prodi = $request->singkatan_prodi;
        $program_studi->id_fakultas = $request->id_fakultas;

        $program_studi->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('prodi.index');
    }

    public function edit(program_studi $prodi)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Ubah Prodi';
        $fakultas = Fakultasn::all();

        return view('pages.edit-prodi', [
            'title' => $title,
            'prodi' => $prodi,
            'fakultas' => $fakultas,
            'type_menu' => 'masterdata',
            'sub_menu' => 'prodi',
        ]);
    }

    public function update(program_studi $prodi, Request $request)
    {
        $request->validate([
            'nama_prodi' => 'required',
            'id_fakultas' => 'required|string|max:50',
            'singkatan_prodi' => 'required|string|max:10',
        ]);

        $prodi->nama_prodi = $request->nama_prodi;
        $prodi->id_fakultas = $request->id_fakultas;
        $prodi->singkatan_prodi = $request->singkatan_prodi;
        
        $prodi->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('prodi.index');
    }

    public function destroy(program_studi $prodi)
    {
        $prodi->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('prodi.index');
    }
}