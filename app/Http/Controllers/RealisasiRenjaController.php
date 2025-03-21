<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class RealisasiRenjaController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'prodi' && Auth::user()->role !== 'unit kerja') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $title = 'Data Realisasi Renja';
        $q = $request->query('q');
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();

        $query = RencanaKerja::with('tahunKerja', 'UnitKerja', 'targetIndikators.indikatorKinerja')
            ->where('rk_nama', 'like', '%' . $q . '%')
            ->whereHas('tahunKerja', function ($subQuery) {
                $subQuery->where('th_is_aktif', 'y');
            })
            ->orderBy('rk_nama', 'asc');

        if (Auth::user()->role == 'unit kerja') {
            $query->whereHas('UnitKerja', function ($subQuery) {
                $subQuery->where('unit_id', Auth::user()->unit_id);
            });
        }

        if (Auth::user()->role == 'prodi') {
            $query->join('rencana_kerja_program_studi', 'rencana_kerja.rk_id', '=', 'rencana_kerja_program_studi.rk_id')
                  ->where('rencana_kerja_program_studi.prodi_id', Auth::user()->prodi_id);
        }

        $rencanaKerjas = $query->paginate(10);
        $no = $rencanaKerjas->firstItem();

        return view('pages.index-realisasirenja', [
            'title' => $title,
            'rencanaKerjas' => $rencanaKerjas,
            'q' => $q,
            'tahuns' => $tahuns,
            'no' => $no,
            'type_menu' => 'realisasirenja',
        ]); 
    }
    
    public function showRealisasi($rk_id)
    {
        $rk_id = explode(',', $rk_id);

        $rencanaKerja = RencanaKerja::whereIn('rk_id', $rk_id)->get();

        $realisasi = RealisasiRenja::whereIn('rk_id', $rk_id)
            ->orderBy('rkr_capaian', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.index-detail-realisasi', [
            'rencanaKerja' => $rencanaKerja,
            'realisasi' => $realisasi,
            'type_menu' => 'realisasirenja',
        ]);
    }


    public function create(Request $request)
    {
        $title = 'Tambah Realisasi Renja';

        $rencanaKerja = RencanaKerja::with('periodes')->findOrFail($request->rk_id); 

        return view('pages.create-realisasirenja', [
            'title' => $title,
            'rencanaKerja' => $rencanaKerja,
            'rk_nama' => $rencanaKerja->rk_nama,
            'pm_nama' => optional($rencanaKerja->periode)->pm_nama,
            'type_menu' => 'realisasirenja',
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'rkr_url' => 'nullable|url',
            'rkr_deskripsi' => 'nullable|string',
            'rkr_capaian' => 'required|integer',
            'rkr_tanggal' => 'required|date',
            'pm_id' => 'nullable',
        ]);


        $realisasi = new RealisasiRenja();
        $realisasi->rk_id = $request->rk_id;
        $realisasi->rkr_url = $request->rkr_url;

        $realisasi->rkr_id = 'RKR' . md5(uniqid(rand(), true));

        $realisasi->rkr_deskripsi = $request->rkr_deskripsi;
        $realisasi->rkr_capaian = $request->rkr_capaian;
        $realisasi->rkr_tanggal = $request->rkr_tanggal;
        $realisasi->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');
        return redirect()->route('realisasirenja.showRealisasi', $realisasi->rk_id);
    }



    public function edit($id)
    {
        $realisasi = RealisasiRenja::findOrFail($id);
        $rencanaKerja = RencanaKerja::with('periodes')->findOrFail($realisasi->rk_id);

        return view('pages.edit-realisasirenja', [
            'title' => 'Edit Realisasi Renja',
            'realisasi' => $realisasi,
            'rencanaKerja' => $rencanaKerja,
            'rk_nama' => $rencanaKerja->rk_nama,
            'pm_nama' => optional($rencanaKerja->periode)->pm_nama,
            'type_menu' => 'realisasirenja',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rkr_url' => 'nullable|url',
            'rkr_deskripsi' => 'nullable|string',
            'rkr_capaian' => 'required|integer',
            'rkr_tanggal' => 'required|date',
            'pm_id' => 'nullable',
        ]);

        $realisasi = RealisasiRenja::findOrFail($id);

        $realisasi->rk_id = $request->rk_id;
        $realisasi->rkr_url = $request->rkr_url;

        $realisasi->rkr_deskripsi = $request->rkr_deskripsi;
        $realisasi->rkr_capaian = $request->rkr_capaian;
        $realisasi->rkr_tanggal = $request->rkr_tanggal;
        $realisasi->save();

        Alert::success('Sukses', 'Data Berhasil Diperbarui');
        return redirect()->route('realisasirenja.showRealisasi', $realisasi->rk_id);
    }

    public function destroy($rkr_id)
    {
        $realisasi = RealisasiRenja::find($rkr_id);

        if (!$realisasi) {
            return redirect()->back()->withErrors('Data tidak ditemukan.');
        }

        if ($realisasi->rkr_file && Storage::exists('public/' . $realisasi->rkr_file)) {
            Storage::delete('public/' . $realisasi->rkr_file);
        }

        $realisasi->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('realisasirenja.showRealisasi', $realisasi->rk_id);
    }

}