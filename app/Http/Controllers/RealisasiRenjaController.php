<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use App\Models\program_studi;
use App\Models\UnitKerja;
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
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');

        // $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $tahuns = tahun_kerja::orderBy('th_tahun', 'desc')->get();
        $units = UnitKerja::orderBy('unit_nama', 'asc')->get();
        $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();

        $query = RencanaKerja::with([
            'tahunKerja',
            'UnitKerja',
            'targetIndikators.indikatorKinerja',
            'programStudis', // relasi program studi
        ])
        ->whereHas('tahunKerja', function ($subQuery) {
            $subQuery->where('th_is_aktif', 'y');
        })
        ->orderBy('rk_nama', 'asc');

        // Filter berdasarkan pencarian
        if (!empty($q)) {
            $query->where(function ($searchQuery) use ($q) {
                $searchQuery->where('rk_nama', 'like', '%' . $q . '%')
                    ->orWhereHas('targetIndikators.indikatorKinerja', function ($subQuery) use ($q) {
                        $subQuery->where('ik_kode', 'like', '%' . $q . '%')
                                ->orWhere('ik_nama', 'like', '%' . $q . '%');
                    });
            });
        }

        if (Auth::user()->role == 'unit kerja') {
            $query->whereHas('UnitKerja', function ($subQuery) {
                $subQuery->where('unit_id', Auth::user()->unit_id);
            });
        }

        if (Auth::user()->role == 'prodi') {
            $query->whereHas('programStudis', function ($subQuery) {
                $subQuery->where('rencana_kerja_program_studi.prodi_id', Auth::user()->prodi_id);
            });
            
        }

        if (!empty($tahunId)) {
            $query->where('rencana_kerja.th_id', $tahunId);
        }

        if (!empty($request->unit_id)) {
            $query->where('rencana_kerja.unit_id', $request->unit_id);
        }

        if (!empty($prodiId)) {
            $query->whereHas('programStudis', function ($subQuery) use ($prodiId) {
                $subQuery->where('rencana_kerja_program_studi.prodi_id', $prodiId);
            });
        }

        $rencanaKerjas = $query->paginate(10)->withQueryString();
        $no = $rencanaKerjas->firstItem();

        return view('pages.index-realisasirenja', [
            'title' => $title,
            'rencanaKerjas' => $rencanaKerjas,
            'q' => $q,
            'units' => $units, 
            'prodis' => $prodis,  
            'prodiId' => $prodiId,
            'tahunId' => $tahunId,
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