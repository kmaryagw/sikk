<?php

namespace App\Http\Controllers;

use App\Models\standar;
use App\Models\standar_dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class StandarController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {   
        $title = 'Data Unit';
        $q = $request->query('q');
        $namaFilter = $request->query('nama');
        $kategoriFilter = $request->query('kategori');

        $query = Standar::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('std_nama', 'like', '%' . $q . '%')
                    ->orWhere('std_deskripsi', 'like', '%' . $q . '%')
                    ->orWhere('std_kategori', 'like', '%' . $q . '%');
            });
        }

        if ($namaFilter) {
            $query->where('std_nama', $namaFilter);
        }

        if ($kategoriFilter) {
            $query->where('std_kategori', $kategoriFilter);
        }

        $standars = $query->orderBy('std_nama', 'asc')->paginate(10)->withQueryString();
        $no = $standars->firstItem();

        $namaStandarList = Standar::select('std_nama')->distinct()->orderBy('std_nama')->pluck('std_nama');
        $kategoriStandarList = Standar::select('std_kategori')->distinct()->orderBy('std_kategori')->pluck('std_kategori');

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.standar_table', compact('standars', 'no'))->render(),
                'pagination' => view('pages.standar_pagination', compact('standars'))->render(),
            ]);
        }

        return view('pages.index-standar', [
            'title' => $title,
            'standars' => $standars,
            'q' => $q,
            'namaStandarList' => $namaStandarList,
            'kategoriStandarList' => $kategoriStandarList,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'standar',
        ]);
    }


    public function create()
    {   
        $title = 'Tambah Standar';

        return view('pages.create-standar', [
            'title' => $title,
            'type_menu' => 'masterdata',
            'sub_menu' => 'standar',

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'std_kategori' => 'required|string|max:255',
            'std_nama' => 'required|string|max:255',
            'std_deskripsi' => 'required',
            'std_url' => 'nullable|url',
        ]);

        $customPrefix = 'STD';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $std_id = $customPrefix . strtoupper($md5Hash);

        $standar = new standar();
        $standar->std_id = $std_id;
        $standar->std_kategori = $request->std_kategori;
        $standar->std_nama = $request->std_nama;
        $standar->std_deskripsi = $request->std_deskripsi;
        $standar->std_url = $request->std_url;
        $standar->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');
        return redirect()->route('standar.index');
    }


    public function edit(standar $standar)
    {   
        $title = 'Ubah Standar';

        return view('pages.edit-standar', [
            'title' => $title,
            'standar' => $standar,
            'type_menu' => 'masterdata',
            'sub_menu' => 'standar',
        ]);
    }

    public function update(standar $standar, Request $request)
    {
        $request->validate([
            'std_kategori' => 'required|string|max:255',
            'std_nama' => 'required|string|max:250',
            'std_deskripsi' => 'required',
            'std_url' => 'nullable|url',
        ]);

        $standar->std_kategori = $request->std_kategori;
        $standar->std_nama = $request->std_nama;
        $standar->std_deskripsi = $request->std_deskripsi;
        $standar->std_url = $request->std_url;
        $standar->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');
        return redirect()->route('standar.index');
    }



    public function destroy(standar $standar)
    {
        $standar->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('standar.index');
    }

}
