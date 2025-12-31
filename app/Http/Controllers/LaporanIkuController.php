<?php

namespace App\Http\Controllers;

use App\Exports\IkuExport;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use App\Models\program_studi;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\target_indikator;
use App\Models\UnitKerja;
use App\Exports\LaporanIkuBdExport;
use App\Exports\LaporanIkuIfExport;
use App\Exports\LaporanIkuDkvExport;
use App\Exports\LaporanIkuRskExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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
        $title   = 'Data Target Capaian';
        $q       = $request->query('q');
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');
        $unitId  = $request->query('unit');

        $tahuns = tahun_kerja::orderBy('th_tahun', 'desc')->get();
        $prodis = program_studi::all();
        $units  = UnitKerja::all();

        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            $tahunId = $tahunAktif?->th_id;
        }

        // 1. Tambahkan monitoringDetail ke dalam eager loading agar status & capaian muncul
        $query = target_indikator::with([
                'indikatorKinerja.unitKerja', 
                'prodi', 
                'tahunKerja',
                'monitoringDetail' 
            ])
            ->join('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->join('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->join('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama', 
                'indikator_kinerja.ik_kode', 
                'program_studi.nama_prodi', 
                'tahun_kerja.th_tahun'
            )
            ->orderBy('target_indikator.ti_target', 'asc');

        if ($q) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $q . '%');
        }

        if ($tahunId) {
            $query->where('target_indikator.th_id', $tahunId);
        }

        if ($prodiId) {
            $query->where('target_indikator.prodi_id', $prodiId);
        }

        if ($unitId) {
            $query->whereHas('indikatorKinerja.unitKerja', function ($queryUnit) use ($unitId) {
                $queryUnit->where('unit_kerja.unit_id', $unitId);
            });
        }

        $target_capaians = $query->get();

        return view('pages.index-laporan-iku', [
            'title'          => $title,
            'target_capaians'=> $target_capaians,
            'tahuns'         => $tahuns,
            'prodis'         => $prodis,
            'units'          => $units, 
            'tahunId'        => $tahunId,
            'prodiId'        => $prodiId,
            'unitId'         => $unitId,
            'q'              => $q,
            'type_menu'      => 'laporan',
            'sub_menu'       => 'laporan-iku',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $tahunId = $request->tahun;
        $prodiId = $request->prodi;
        $unitId  = $request->unit;
        $keyword = $request->q;

        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            $tahunId = $tahunAktif?->th_id;
        }

        $tahunText = $tahunId ? tahun_kerja::find($tahunId)?->th_tahun : 'Semua-Tahun';
        $prodiText = $prodiId ? program_studi::find($prodiId)?->nama_prodi : 'Semua-Prodi';
        $unitText  = $unitId ? UnitKerja::find($unitId)?->unit_nama : 'Semua-Unit';

        $tanggal = now()->format('Ymd_His');

        // Sanitasi agar tidak mengandung karakter ilegal
        $sanitize = fn($string) => preg_replace('/[\/\\\\]+/', '-', str_replace(' ', '-', $string ?? ''));

        // Nama file rapi + informatif
        $filename = "Laporan_IKU_" 
            . $sanitize($tahunText) . "_" 
            . $sanitize($prodiText) . "_" 
            . $sanitize($unitText) . "_" 
            . $tanggal . ".xlsx";

        // Ekspor Excel
        return Excel::download(
            new IkuExport(
                $tahunId,
                $prodiId,
                $unitId,
                $keyword
            ),
            $filename
        );
    }

    public function exportPdf(Request $request)
    {
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');
        $unitId  = $request->query('unit');
        $keyword = $request->query('q');

        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            $tahunId = $tahunAktif?->th_id;
        }

        $query = target_indikator::with([
                'tahunKerja',
                'prodi',
                'indikatorKinerja.unitKerja', 
                'monitoringDetail'
            ])
            ->select('target_indikator.*') 
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->orderBy('target_indikator.ti_target', 'asc');

        // 2. Filter-filter
        if ($tahunId) {
            $query->where('target_indikator.th_id', $tahunId);
        }
        if ($prodiId) {
            $query->where('target_indikator.prodi_id', $prodiId);
        }
        if ($unitId) {
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) use ($unitId) {
                $q->where('unit_kerja.unit_id', $unitId);
            });
        }
        if ($keyword) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $keyword . '%');
        }

        $target_capaians = $query->get();

        // 3. Data Header
        $tahun = $tahunId ? tahun_kerja::find($tahunId)?->th_tahun : 'Semua-Tahun';
        $prodi = $prodiId ? program_studi::find($prodiId)?->nama_prodi : 'Semua-Prodi';
        $unit  = $unitId ? UnitKerja::find($unitId)?->unit_nama : 'Semua-Unit';

        $sanitize = fn($string) => preg_replace('/[\/\\\\]+/', '-', str_replace(' ', '-', $string ?? ''));

        $namaFile = "Laporan_IKU_" . $sanitize($tahun) . "_" . $sanitize($prodi) . "_" . $sanitize($unit) . ".pdf";
        $pdf = Pdf::loadView('export.laporan-iku-pdf', [
            'target_capaians' => $target_capaians,
            'tahun' => $tahun,
            'prodi' => $prodi,
            'unit'  => $unit,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($namaFile);
    }

}
