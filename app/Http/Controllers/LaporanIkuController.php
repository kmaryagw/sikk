<?php

namespace App\Http\Controllers;

use App\Exports\IkuExport;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use App\Models\program_studi;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\target_indikator;
use App\Exports\LaporanIkuBdExport;
use App\Exports\LaporanIkuIfExport;
use App\Exports\LaporanIkuDkvExport;
use App\Exports\LaporanIkuRskExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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

        $tahuns = tahun_kerja::orderBy('th_tahun', 'desc')->get();
        $prodis = program_studi::all();

        // Jika tidak ada filter tahun dikirim, ambil tahun aktif
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            $tahunId = $tahunAktif?->th_id;
        }

        $query = target_indikator::select(
                'target_indikator.*', 
                'indikator_kinerja.ik_nama', 
                'program_studi.nama_prodi', 
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->orderBy('ti_target', 'asc');

        if ($q) {
            $query->where('ik_nama', 'like', '%' . $q . '%');
        }

        if ($tahunId) {
            $query->where('target_indikator.th_id', $tahunId);
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

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new IkuExport($request->query('tahun'), $request->query('prodi'), $request->query('q')),
            'laporan_iku.xlsx'
        );
    }

    public function exportExcelIF(Request $request)
    {
        return Excel::download(
            new LaporanIkuIfExport($request->query('tahun'), $request->query('q')),
            'laporan_iku_if.xlsx'
        );
    }

    public function exportExcelRSK(Request $request)
    {
        return Excel::download(
            new LaporanIkuRskExport($request->query('tahun'), $request->query('q')),
            'laporan_iku_rsk.xlsx'
        );
    }

    public function exportExcelBD(Request $request)
    {
        return Excel::download(
            new LaporanIkuBdExport($request->query('tahun'), $request->query('q')),
            'laporan_iku_bd.xlsx'
        );
    }

    public function exportExcelDKV(Request $request)
    {
        return Excel::download(
            new LaporanIkuDkvExport($request->query('tahun'), $request->query('q')),
            'laporan_iku_dkv.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');
        $keyword = $request->query('q');

        // Gunakan tahun aktif jika parameter tahun kosong
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $tahunId = $tahunAktif->th_id;
            }
        }

        $tahun = $tahunId ? tahun_kerja::find($tahunId) : null;

        // Validasi: jika tahunId ada tapi tidak ditemukan di DB
        if ($tahunId && !$tahun) {
            abort(404, 'Tahun tidak ditemukan.');
        }

        $query = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id');

        if ($tahunId) {
            $query->where('tahun_kerja.th_id', $tahunId);
        }

        if ($prodiId) {
            $query->where('program_studi.prodi_id', $prodiId);
        }

        if ($keyword) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $keyword . '%');
        }

        $target_capaians = $query->get();

        $namaFile = 'laporan_iku';
        if ($tahun) {
            $namaFile .= '_' . str_replace('/', '-', $tahun->th_tahun);
        }
        $namaFile .= '.pdf';

        $pdf = Pdf::loadView('export.laporan-iku-pdf', [
            'target_capaians' => $target_capaians,
            'tahun' => $tahun,
        ]);

        return $pdf->download($namaFile);
    }

    public function exportPdfIF(Request $request)
    {
        $tahunId = $request->query('tahun');
        $keyword = $request->query('q');

        // Jika tidak ada tahun yang dikirimkan, ambil tahun aktif
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $tahunId = $tahunAktif->th_id;
            }
        }

        // Validasi apakah tahun ada
        if ($tahunId && !tahun_kerja::find($tahunId)) {
            abort(404, 'Tahun tidak ditemukan.');
        }

        $query = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('program_studi.nama_prodi', 'Informatika');

        if ($tahunId) {
            $query->where('tahun_kerja.th_id', $tahunId);
            $tahun = tahun_kerja::find($tahunId);
        } else {
            $tahun = null;
        }

        if ($keyword) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $keyword . '%');
        }

        $target_capaians = $query->get();

        $namaFile = 'laporan_iku_if';
        if ($tahun) {
            $namaFile .= '_' . str_replace('/', '-', $tahun->th_tahun);
        }
        $namaFile .= '.pdf';

        $pdf = Pdf::loadView('export.laporan-iku-if-pdf', [
            'target_capaians' => $target_capaians,
            'namaProdi' => 'Informatika',
            'tahun' => $tahun,
        ]);

        return $pdf->download($namaFile);
    }

    public function exportPdfRsk(Request $request)
    {
        $tahunId = $request->query('tahun');
        $keyword = $request->query('q');

        // Jika tidak ada tahun yang dikirimkan, ambil tahun aktif
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $tahunId = $tahunAktif->th_id;
            }
        }

        // Validasi apakah tahun ada
        if ($tahunId && !tahun_kerja::find($tahunId)) {
            abort(404, 'Tahun tidak ditemukan.');
        }

        $query = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('program_studi.nama_prodi', 'Rekayasa Sistem Komputer');

        if ($tahunId) {
            $query->where('tahun_kerja.th_id', $tahunId);
            $tahun = tahun_kerja::find($tahunId);
        } else {
            $tahun = null;
        }

        if ($keyword) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $keyword . '%');
        }

        $target_capaians = $query->get();

        $namaFile = 'laporan_iku_rsk';
        if ($tahun) {
            $namaFile .= '_' . str_replace('/', '-', $tahun->th_tahun);
        }
        $namaFile .= '.pdf';

        $pdf = Pdf::loadView('export.laporan-iku-rsk-pdf', [
            'target_capaians' => $target_capaians,
            'namaProdi' => 'Rekayasa Sistem Komputer',
            'tahun' => $tahun,
        ]);

        return $pdf->download($namaFile);
    }

    public function exportPdfBd(Request $request)
    {
        $tahunId = $request->query('tahun');
        $keyword = $request->query('q');

        // Jika tidak ada tahun yang dikirimkan, ambil tahun aktif
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $tahunId = $tahunAktif->th_id;
            }
        }

        // Validasi apakah tahun ada
        if ($tahunId && !tahun_kerja::find($tahunId)) {
            abort(404, 'Tahun tidak ditemukan.');
        }

        $query = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('program_studi.nama_prodi', 'Bisnis Digital');

        if ($tahunId) {
            $query->where('tahun_kerja.th_id', $tahunId);
            $tahun = tahun_kerja::find($tahunId);
        } else {
            $tahun = null;
        }

        if ($keyword) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $keyword . '%');
        }

        $target_capaians = $query->get();

        $namaFile = 'laporan_iku_bd';
        if ($tahun) {
            $namaFile .= '_' . str_replace('/', '-', $tahun->th_tahun);
        }
        $namaFile .= '.pdf';

        $pdf = Pdf::loadView('export.laporan-iku-bd-pdf', [
            'target_capaians' => $target_capaians,
            'namaProdi' => 'Bisnis Digital',
            'tahun' => $tahun,
        ]);

        return $pdf->download($namaFile);
    }

    public function exportPdfDkv(Request $request)
    {
        $tahunId = $request->query('tahun');
        $keyword = $request->query('q');

        // Jika tidak ada tahun yang dikirimkan, ambil tahun aktif
        if (!$tahunId) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $tahunId = $tahunAktif->th_id;
            }
        }

        // Validasi apakah tahun ada
        if ($tahunId && !tahun_kerja::find($tahunId)) {
            abort(404, 'Tahun tidak ditemukan.');
        }

        $query = target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('program_studi.nama_prodi', 'Desain Komunikasi Visual');

        if ($tahunId) {
            $query->where('tahun_kerja.th_id', $tahunId);
            $tahun = tahun_kerja::find($tahunId);
        } else {
            $tahun = null;
        }

        if ($keyword) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $keyword . '%');
        }

        $target_capaians = $query->get();

        $namaFile = 'laporan_iku_dkv';
        if ($tahun) {
            $namaFile .= '_' . str_replace('/', '-', $tahun->th_tahun);
        }
        $namaFile .= '.pdf';

        $pdf = Pdf::loadView('export.laporan-iku-dkv-pdf', [
            'target_capaians' => $target_capaians,
            'namaProdi' => 'Desain Komunikasi Visual',
            'tahun' => $tahun,
        ]);

        return $pdf->download($namaFile);
    }
}
