<?php

namespace App\Http\Controllers;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\periode_monev;
use Illuminate\Http\Request;

class PeriodeMonevController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Rencana Strategis';
        $q = $request->query('q');
        $periodems = periode_monev::where('pm_nama', 'like', '%' . $q . '%')
        ->paginate(10)
        ->withQueryString();
        $no = $periodems->firstItem();
        
        return view('pages.index-periode-monev', [
            'title' => $title,
            'periodems' => $periodems,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);
    }

    public function create()
    {
        $title = 'Tambah Periode Monev';
        

        return view('pages.create-periode-monev', [
            'title' => $title,
            
            'type_menu' => 'masterdata',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pm_nama' => 'required|string|max:255',
            
        ]);
    
        $customPrefix = 'PM';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $pm_id = $customPrefix . strtoupper($md5Hash);
    
        $pm = new periode_monev($request->all());
        $pm->pm_id = $pm_id;
        
        $pm->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('periode-monev.index');
    }
}
