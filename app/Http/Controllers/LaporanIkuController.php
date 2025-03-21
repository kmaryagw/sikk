<?php

namespace App\Http\Controllers;

use App\Models\program_studi;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LaporanIkuController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $title = 'Data Target Capaian';
        $q = $request->query('q');
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');

        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $prodis = program_studi::all();

        $query = target_indikator::select('target_indikator.*', 'indikator_kinerja.ik_nama', 'program_studi.nama_prodi', 'aktif_tahun.th_tahun')
            ->leftjoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftjoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftjoin('tahun_kerja as aktif_tahun', function ($join) {
                $join->on('aktif_tahun.th_id', '=', 'target_indikator.th_id')
                    ->where('aktif_tahun.th_is_aktif', 'y');
            })
            ->orderBy('ti_target', 'asc');

        if ($q) {
            $query->where('ik_nama', 'like', '%' . $q . '%');
        }

        if ($tahunId) {
            $query->where('aktif_tahun.th_id', $tahunId);
        }

        if ($prodiId) {
            $query->where('program_studi.prodi_id', $prodiId);
        }

        $target_capaians = $query->paginate(10)->withQueryString();
        $no = $target_capaians->firstItem();

        return view('pages.index-laporan-iku', [
            'title' => $title,
            'target_capaians' => $target_capaians,
            'tahuns' => $tahuns,
            'prodis' => $prodis,
            'tahunId' => $tahunId,
            'prodiId' => $prodiId,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'laporan',
            'sub_menu' => 'laporan-iku',
        ]);
    }

    public function exportExcel()
    {
        return Excel::download(new \App\Exports\IkuExport, 'laporan_iku.xlsx');
    }

//     public function exportExcel(Request $request)
// {
//     $q = $request->query('q');
//     $tahunId = $request->query('tahun');
//     $prodiId = $request->query('prodi');

//     $query = target_indikator::select('target_indikator.*', 'indikator_kinerja.ik_nama', 'program_studi.nama_prodi', 'tahun_kerja.th_tahun')
//         ->leftjoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
//         ->leftjoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
//         ->leftjoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
//         ->where('tahun_kerja.th_is_aktif', 'y');

//     if ($q) {
//         $query->where('ik_nama', 'like', '%' . $q . '%');
//     }

//     if ($tahunId) {
//         $query->where('tahun_kerja.th_id', $tahunId);
//     }

//     if ($prodiId) {
//         $query->where('program_studi.prodi_id', $prodiId);
//     }

//     $target_capaians = $query->get();

//     return Excel::download(new \App\Exports\IkuExport($target_capaians), 'laporan_iku.xlsx');
// }


    public function exportPdf()
    {
        $target_capaians = target_indikator::select('target_indikator.*', 'indikator_kinerja.ik_nama', 'program_studi.nama_prodi', 'tahun_kerja.th_tahun')
            ->leftjoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftjoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftjoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('tahun_kerja.th_is_aktif', 'y')
            ->get();
        $pdf = Pdf::loadView('export.laporan-iku-pdf', compact('target_capaians'));
        return $pdf->download('laporan_iku.pdf');
    }
}
