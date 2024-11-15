<?php

namespace App\Http\Controllers;

use App\Models\RencanaKerja;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use App\Models\tahun_kerja;
use App\Models\UnitKerja;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanRenjaController extends Controller
{

    public function index(Request $request)
    {
        $units = UnitKerja::all();
        $tahuns = tahun_kerja::all();
    
        $title = 'Data Realisasi Renja';
        $q = $request->query('q');
        $unit_id = $request->query('unit_id');
        $tahun = $request->query('tahun');

        $rencanaKerjas = RencanaKerja::with('tahunKerja', 'UnitKerja')
            ->where('rk_nama', 'like', '%' . $q . '%')
            ->when($unit_id, function ($query) use ($unit_id) {
                return $query->where('unit_id', $unit_id);
            })
            ->when($tahun, function ($query) use ($tahun) {
                return $query->where('th_id', $tahun);
            })
            ->orderBy('rk_nama', 'asc')
            ->paginate(10);

        $no = $rencanaKerjas->firstItem();

        return view('pages.index-laporan-renja', [
            'title' => $title,
            'rencanaKerjas' => $rencanaKerjas,
            'q' => $q,
            'unit_id' => $unit_id,
            'tahun' => $tahun,
            'units' => $units,
            'tahuns' => $tahuns,
            'no' => $no,
            'type_menu' => 'laporan',
            'sub_menu' => 'laporan-renja',
        ]);
    }

    public function exportExcel()
    {
        return Excel::download(new \App\Exports\LaporanExport, 'laporan_renja.xlsx');
    }

    public function exportPdf()
    {
        $rencanaKerjas = RencanaKerja::with('tahunKerja', 'UnitKerja')->get();
        $pdf = Pdf::loadView('export.laporan-renja-pdf', compact('rencanaKerjas'));
        return $pdf->download('laporan_renja.pdf');
    }
}