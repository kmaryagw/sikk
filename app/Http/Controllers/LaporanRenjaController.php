<?php

namespace App\Http\Controllers;

use App\Models\RencanaKerja;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\tahun_kerja;
use App\Models\program_studi;
use App\Models\UnitKerja;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\LaporanRenjaRskExport;
use App\Exports\LaporanRenjaIfExport;
use App\Exports\LaporanRenjaBdExport;
use App\Exports\LaporanRenjaDkvExport;

class LaporanRenjaController extends Controller
{

    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'unit kerja' && Auth::user()->role !== 'prodi' && Auth::user()->role !== 'fakultas') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {

        // dd($request->query());



        $units = UnitKerja::all();
        $tahuns = tahun_kerja::all();
        $prodis = program_studi::all();

    
        $title = 'Data Realisasi Renja';
        $q = $request->query('q');
        $unit_id = $request->query('unit_id');
        $tahun = $request->query('tahun');
        $prodi_id = $request->query('prodi_id');

        $rencanaKerjas = RencanaKerja::with('tahunKerja', 'unitKerja', 'standar', 'programStudis')
            ->when($q, fn($query) => $query->where('rk_nama', 'like', '%' . $q . '%'))
            ->when($unit_id, fn($query) => $query->where('unit_id', $unit_id))
            ->when($tahun, fn($query) => $query->where('th_id', $tahun))
            ->when($prodi_id, function ($query) use ($prodi_id) {
                return $query->whereHas('programStudis', function ($q) use ($prodi_id) {
                    $q->where('program_studi.prodi_id', $prodi_id);
                });
            })            
            ->orderBy('rk_nama', 'asc')
            ->paginate(10);




        $no = $rencanaKerjas->firstItem();


        $totalAnggaran = RencanaKerja::when($q, fn($query) => $query->where('rk_nama', 'like', '%' . $q . '%'))
            ->when($unit_id, fn($query) => $query->where('unit_id', $unit_id))
            ->when($tahun, fn($query) => $query->where('th_id', $tahun))
            ->when($prodi_id, function ($query) use ($prodi_id) {
                return $query->whereHas('programStudis', function ($q) use ($prodi_id) {
                    $q->where('program_studi.prodi_id', $prodi_id);
                });
            })            
            ->sum('anggaran');


        return view('pages.index-laporan-renja', [
            'title' => $title,
            'rencanaKerjas' => $rencanaKerjas,
            'q' => $q,
            'unit_id' => $unit_id,
            'tahun' => $tahun,
            'prodi_id' => $prodi_id,
            'units' => $units,
            'tahuns' => $tahuns,
            'prodis' => $prodis,
            'no' => $no,
            'totalAnggaran' => $totalAnggaran,
            'type_menu' => 'laporan',
            'sub_menu' => 'laporan-renja',
            'namaProdi' => 'Informatika','Rekayasa Sistem Komputer','Bisnis Digital','Desain Komunikasi Visual',
        ]);
    }

    public function exportExcel()
    {
        return Excel::download(new \App\Exports\LaporanExport, 'laporan_renja.xlsx');
    }

    public function exportExcelRsk()
    {
        return Excel::download(new LaporanRenjaRskExport, 'laporan_renja_rsk.xlsx');
    }

    public function exportExcelIf()
    {
        return Excel::download(new LaporanRenjaIfExport, 'laporan_renja_if.xlsx');
    }

    public function exportExcelBd()
    {
        return Excel::download(new LaporanRenjaBdExport, 'laporan_renja_bd.xlsx');
    }

    public function exportExcelDkv()
    {
        return Excel::download(new LaporanRenjaDkvExport, 'laporan_renja_dkv.xlsx');
    }

    

    public function exportPdf()
    {
        $rencanaKerjas = RencanaKerja::with('tahunKerja', 'UnitKerja')->get();
        $pdf = Pdf::loadView('export.laporan-renja-pdf', compact('rencanaKerjas'));
        return $pdf->download('laporan_renja.pdf');
    }    

    public function exportPdfInformatika()
    {
        $rencanaKerjas = RencanaKerja::with(['tahunKerja', 'UnitKerja', 'programStudis', 'periodes'])
            ->whereHas('programStudis', function ($query) {
                $query->where('nama_prodi', 'Informatika');
            })
            ->get();

            $pdf = Pdf::loadView('export.laporan-renja-if-pdf', [
                'rencanaKerjas' => $rencanaKerjas,
                'namaProdi' => 'Informatika',
            ]);
            
        return $pdf->download('laporan-renja-if.pdf');
    }

    public function exportPdfRSK()
    {
        $rencanaKerjas = RencanaKerja::with(['tahunKerja', 'UnitKerja', 'programStudis', 'periodes'])
            ->whereHas('programStudis', function ($query) {
                $query->where('nama_prodi', 'Rekayasa Sistem Komputer');
            })
            ->get();

            $pdf = Pdf::loadView('export.laporan-renja-rsk-pdf', [
                'rencanaKerjas' => $rencanaKerjas,
                'namaProdi' => 'Rekayasa Sistem Komputer',
            ]);
            
        return $pdf->download('laporan_renja_rsk.pdf');
    }

    public function exportPdfBD()
    {
        $rencanaKerjas = RencanaKerja::with(['tahunKerja', 'UnitKerja', 'programStudis', 'periodes'])
            ->whereHas('programStudis', function ($query) {
                $query->where('nama_prodi', 'Bisnis Digital');
            })
            ->get();

            $pdf = Pdf::loadView('export.laporan-renja-bd-pdf', [
                'rencanaKerjas' => $rencanaKerjas,
                'namaProdi' => 'Bisnis Digital',
            ]);
            
        return $pdf->download('laporan_renja_bd.pdf');
    }

    public function exportPdfDKV()
    {
        $rencanaKerjas = RencanaKerja::with(['tahunKerja', 'UnitKerja', 'programStudis', 'periodes'])
            ->whereHas('programStudis', function ($query) {
                $query->where('nama_prodi', 'Desain Komunikasi Visual');
            })
            ->get();

            $pdf = Pdf::loadView('export.laporan-renja-dkv-pdf', [
                'rencanaKerjas' => $rencanaKerjas,
                'namaProdi' => 'Desain Komunikasi Visual',
            ]);
            
        return $pdf->download('laporan_renja_dkv.pdf');
    }

}