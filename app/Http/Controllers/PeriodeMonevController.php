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
        'pm_nama' => 'required|string|max:255',  // Validasi untuk pm_nama
    ]);

    // Generate custom ID
    $customPrefix = 'PM';
    $timestamp = time(); // Ambil timestamp sebagai basis untuk ID
    $md5Hash = md5($timestamp);  // Hash dari timestamp
    $pm_id = $customPrefix . strtoupper($md5Hash);  // ID yang di-generate

    // Cek apakah ID sudah benar
      // Debug log untuk memeriksa pm_id

    // Create new instance and assign values
    $pm = new periode_monev();
    $pm->pm_id = $pm_id;  // Set custom ID
    $pm->pm_nama = $request->pm_nama;
    
    $pm->save();

    Alert::success('Sukses', 'Data Berhasil Ditambah');
    return redirect()->route('periodemonev.index');
}

public function edit(periode_monev $periodemonev)
{
    $title = 'Ubah Periode Monev';
    return view('pages.edit-periode-monev', [
        'title' => $title,
        'periodemonev' => $periodemonev,
        'type_menu' => 'masterdata',
    ]);
}

    public function update(periode_monev $periodemonev, Request $request)
    {
        $request->validate([
            'pm_nama' => 'required',
            
            
        ]);
        $periodemonev->pm_nama = $request->pm_nama;
        $periodemonev->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('periodemonev.index');
    }

    public function destroy(periode_monev $periodemonev)
    {
        $periodemonev->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('periodemonev.index');
    }
}
