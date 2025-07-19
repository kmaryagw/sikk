<?php

namespace App\Http\Controllers;

use App\Models\program_studi;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanIkuIfExport;
use App\Exports\LaporanIkuRskExport;
use App\Exports\LaporanIkuBdExport;
use App\Exports\LaporanIkuDkvExport;

class LaporanIkuController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'unit kerja' && Auth::user()->role !== 'prodi' && Auth::user()->role !== 'fakultas') {
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

    public function exportExcelIF()
    {
        return Excel::download(new LaporanIkuIfExport, 'laporan_iku_if.xlsx');
    }

    public function exportExcelRSK()
    {
        return Excel::download(new LaporanIkuRskExport, 'laporan_iku_rsk.xlsx');
    }

    public function exportExcelBD()
    {
        return Excel::download(new LaporanIkuBdExport, 'laporan_iku_bd.xlsx');
    }

    public function exportExcelDKV()
    {
        return Excel::download(new LaporanIkuDkvExport, 'laporan_iku_dkv.xlsx');
    }

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

    public function exportPdfIF()
    {
        $target_capaians = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('tahun_kerja.th_is_aktif', 'y')
            ->where('program_studi.nama_prodi', 'Informatika')
            ->get();

        $pdf = Pdf::loadView('export.laporan-iku-if-pdf', [
            'target_capaians' => $target_capaians,
            'namaProdi' => 'Informatika',
        ]);
        return $pdf->download('laporan_iku_if.pdf');
    }

    public function exportPdfRsk()
    {
        $target_capaians = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('tahun_kerja.th_is_aktif', 'y')
            ->where('program_studi.nama_prodi', 'Rekayasa Sistem Komputer')
            ->get();

        $pdf = Pdf::loadView('export.laporan-iku-rsk-pdf', [
            'target_capaians' => $target_capaians,
            'namaProdi' => 'Rekayasa Sistem Komputer',
        ]);
        return $pdf->download('laporan_iku_rsk.pdf');
    }

    public function exportPdfBd()
    {
        $target_capaians = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('tahun_kerja.th_is_aktif', 'y')
            ->where('program_studi.nama_prodi', 'Bisnis Digital')
            ->get();

        $pdf = Pdf::loadView('export.laporan-iku-bd-pdf', [
            'target_capaians' => $target_capaians,
            'namaProdi' => 'Bisnis Digital',
        ]);
        return $pdf->download('laporan_iku_bd.pdf');
    }

    public function exportPdfDkv()
    {
        $target_capaians = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('tahun_kerja.th_is_aktif', 'y')
            ->where('program_studi.nama_prodi', 'Desain Komunikasi Visual')
            ->get();

        $pdf = Pdf::loadView('export.laporan-iku-dkv-pdf', [
            'target_capaians' => $target_capaians,
            'namaProdi' => 'Desain Komunikasi Visual',
        ]);
        return $pdf->download('laporan_iku_dkv.pdf');
    }
}
