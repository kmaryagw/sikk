<?php

namespace App\Http\Controllers;

use App\Models\Fakultasn;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class FakultasnController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Falkutas';
        $q = $request->query('q');
        $fakultasns = Fakultasn::where('nama_fakultas', 'like', '%' . $q . '%')
        ->orderBy('nama_fakultas', 'asc')
        ->paginate(10)
        ->withQueryString();
        $no = $fakultasns->firstItem();
        
        return view('pages.index-fakultasn', [
            'title' => $title,
            'fakultasns' => $fakultasns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'fakultas',
        ]);

    }

        public function create()
    {
        $title = 'Tambah Fakultas';

        return view('pages.create-fakultasn', [
            'title' => $title,
            'type_menu' => 'masterdata',
            'sub_menu' => 'fakultas',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fakultas' => 'required|string|max:255',
        ]);
    
        $customPrefix = 'FK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $id_fakultas = $customPrefix . strtoupper($md5Hash);
    
        $fakultasn = new Fakultasn();
        $fakultasn->id_fakultas = $id_fakultas;
        $fakultasn->nama_fakultas = $request->nama_fakultas;
    
        $fakultasn->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('fakultasn.index');
    }

    public function edit($id)
    {
        $title = 'Ubah Fakultas';
        $fakultasn = Fakultasn::findOrFail($id);
    
    
    return view('pages.edit-fakultasn', [
        'title' => $title,
        'fakultasn' => $fakultasn,
        'type_menu' => 'masterdata',
        'sub_menu' => 'fakultas',
    ]);
    }

    public function update(Fakultasn $fakultasn, Request $request)
    {
        $request->validate([
            'nama_fakultas' => 'required',
        ]);
    
        $fakultasn->nama_fakultas = $request->nama_fakultas;
        $fakultasn->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');
        return redirect()->route('fakultasn.index');
    }

    public function destroy(Fakultasn $fakultasn)
    {
        $fakultasn->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('fakultasn.index');
    }
}
