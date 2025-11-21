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
        $unitId  = $request->query('unit'); // 🔹 filter unit kerja

        $tahuns = tahun_kerja::orderBy('th_tahun', 'desc')->get();
        $prodis = program_studi::all();
        $units  = UnitKerja::all(); // 🔹 ambil semua unit kerja

        // Jika tidak ada filter tahun, ambil tahun aktif
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            $tahunId = $tahunAktif?->th_id;
        }

        $query = target_indikator::select(
                'target_indikator.*', 
                'indikator_kinerja.ik_nama', 
                'program_studi.nama_prodi', 
                'tahun_kerja.th_tahun',
                'uk.unit_nama' // 🔹 pakai alias
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->leftJoin('unit_kerja as uk', 'uk.unit_id', '=', 'indikator_kinerja.unit_id') // 🔹 join dengan alias
            ->orderBy('ti_target', 'asc');

        if ($q) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $q . '%');
        }

        if ($tahunId) {
            $query->where('target_indikator.th_id', $tahunId);
        }

        if ($prodiId) {
            $query->where('program_studi.prodi_id', $prodiId);
        }

        if ($unitId) { // 🔹 filter unit kerja
            // Menggunakan relasi many-to-many dengan unitKerja
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) use ($unitId) {
                $q->where('unit_kerja.unit_id', $unitId); // Menggunakan alias unit_kerja untuk filter
            });
        }

        $target_capaians = $query->paginate(10)->withQueryString();
        $no = $target_capaians->firstItem();

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
            'no'             => $no,
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

        // Jika user tidak memilih tahun → gunakan tahun aktif
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            $tahunId = $tahunAktif?->th_id;
        }

        // Ambil label untuk nama file
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

        // Pakai tahun aktif ketika user tidak memilih
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            $tahunId = $tahunAktif?->th_id;
        }

        // Query laporan PDF
        $query = target_indikator::select(
                'tahun_kerja.th_tahun',
                'program_studi.nama_prodi',
                'uk.unit_nama',
                'indikator_kinerja.ik_nama',
                'target_indikator.ti_target',
                'monitoring_iku_detail.mtid_capaian',
                'monitoring_iku_detail.mtid_status'
            )
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->leftJoin('monitoring_iku_detail', 'monitoring_iku_detail.ti_id', '=', 'target_indikator.ti_id')
            ->leftJoin('unit_kerja as uk', 'uk.unit_id', '=', 'indikator_kinerja.unit_id');

        // Filter tahun
        if ($tahunId) {
            $query->where('tahun_kerja.th_id', $tahunId);
        }

        // Filter prodi
        if ($prodiId) {
            $query->where('program_studi.prodi_id', $prodiId);
        }

        // Filter unit
        if ($unitId) {
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) use ($unitId) {
                $q->where('unit_kerja.unit_id', $unitId);
            });
        }

        // Filter keyword
        if ($keyword) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $keyword . '%');
        }

        $target_capaians = $query->get();

        // Detail nama file
        $tahun = $tahunId ? tahun_kerja::find($tahunId)?->th_tahun : 'Semua-Tahun';
        $prodi = $prodiId ? program_studi::find($prodiId)?->nama_prodi : 'Semua-Prodi';
        $unit  = $unitId ? UnitKerja::find($unitId)?->unit_nama : 'Semua-Unit';

        // Sanitasi nama file agar tidak ada karakter ilegal
        $sanitize = fn($string) => preg_replace('/[\/\\\\]+/', '-', str_replace(' ', '-', $string ?? ''));

        // Nama file lebih rapi & tidak doble .pdf
        $namaFile = "Laporan_IKU_" 
            . $sanitize($tahun) . "_" 
            . $sanitize($prodi) . "_" 
            . $sanitize($unit) . "_" 
            . now()->format('Ymd_His')
            . ".pdf";

        $pdf = Pdf::loadView('export.laporan-iku-pdf', [
            'target_capaians' => $target_capaians,
            'tahun' => $tahun,
            'prodi' => $prodi,
            'unit'  => $unit,
        ]);

        return $pdf->download($namaFile);
    }

}
