<?php

namespace App\Http\Controllers;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Renstra;
use Illuminate\Http\Request;

class RenstraController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Rencana Strategis';
        $q = $request->query('q');
        $renstras = Renstra::where('ren_nama', 'like', '%' . $q . '%')
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
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'ren_nama' => 'required|string|max:100',
            'ren_pimpinan' => 'required|string|max:100',
            'ren_periode_awal' => 'required|integer|min:1900|max:' . date('Y'),
            'ren_periode_akhir' => 'required|integer|min:1900|max:' . date('Y'),
            'ren_is_aktif' => 'required|in:y,n',
        ]);

        // Membuat ID unik untuk renstra
        $ren_id = 'REN' . strtoupper(md5(time()));

        // Simpan data ke dalam database
        $renstra = new Renstra($request->all());
        $renstra->ren_id = $ren_id;
        $renstra->save();

        Alert::success('Sukses', 'Renstra berhasil ditambahkan.');
        return redirect()->route('rencana-strategis.index');
    }
}
