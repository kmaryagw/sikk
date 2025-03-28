<?php

namespace App\Http\Controllers;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Renstra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RenstraController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {   
        $title = 'Data Rencana Strategis';
        $q = $request->query('q');
        $renstras = Renstra::where('ren_nama', 'like', '%' . $q . '%')
            ->orderBy('ren_nama', 'asc')
            ->paginate(10)
            ->withQueryString();
        $no = $renstras->firstItem();
        
        return view('pages.index-renstra', [
            'title' => $title,
            'renstras' => $renstras,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);
    }

    public function create()
    {   
        $title = 'Tambah Rencana Strategis';
        
        $ren_is_aktifs = ['y', 'n'];

        return view('pages.create-renstra', [
            'title' => $title,
            'ren_is_aktifs' => $ren_is_aktifs,
            'type_menu' => 'masterdata',
            'sub_menu' => 'renstra',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ren_nama' => 'required|string|max:100',
            'ren_pimpinan' => 'required|string|max:100',
            'ren_periode_awal' => 'required|integer|min:1900|max:2100',
            'ren_periode_akhir' => 'required|integer|min:1900|max:2100',
            'ren_is_aktif' => 'required|in:y,n',
        ]);

        if ($request->ren_is_aktif == 'y') {
            Renstra::where('ren_is_aktif', 'y')->update(['ren_is_aktif' => 'n']);
        }

        $ren_id = 'REN' . strtoupper(md5(time()));

        $renstra = new Renstra($request->all());
        $renstra->ren_id = $ren_id;
        $renstra->save();

        Alert::success('Sukses', 'Data berhasil ditambahkan.');
        return redirect()->route('renstra.index');
    }

    public function edit(Renstra $renstra)
    {        
        $title = 'Ubah Rencana Strategis';
        $ren_is_aktifs = ['y', 'n'];
    
        return view('pages.edit-renstra', [
            'title' => $title,
            'ren_is_aktifs' => $ren_is_aktifs,
            'renstra' => $renstra,
            'type_menu' => 'masterdata',
            'sub_menu' => 'renstra',
        ]);
    }

    public function update(Renstra $renstra, Request $request)
    {
        $request->validate([
            'ren_nama' => 'required|string|max:100',
            'ren_pimpinan' => 'required|string|max:100',
            'ren_periode_awal' => 'required|integer|min:1900|max:2100',
            'ren_periode_akhir' => 'required|integer|min:1900|max:2100',
            'ren_is_aktif' => 'required|in:y,n',
        ]);

        if ($request->ren_is_aktif == 'y') {
            Renstra::where('ren_is_aktif', 'y')
                ->where('ren_id', '!=', $renstra->ren_id)
                ->update(['ren_is_aktif' => 'n']);
        } 

        $renstra->ren_nama = $request->ren_nama;
        $renstra->ren_pimpinan = $request->ren_pimpinan;
        $renstra->ren_periode_awal = $request->ren_periode_awal;
        $renstra->ren_periode_akhir = $request->ren_periode_akhir;
        $renstra->ren_is_aktif = $request->ren_is_aktif;

        $renstra->save();

        Alert::success('Sukses', 'Data Renstra berhasil diubah.');
        return redirect()->route('renstra.index');
    }

    public function destroy(Renstra $renstra)
    {
        $renstra->delete();
        Alert::success('Sukses', 'Data Renstra berhasil dihapus.');
        return redirect()->route('renstra.index');
    }
}
