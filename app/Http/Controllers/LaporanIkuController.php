<?php

namespace App\Http\Controllers;

use App\Models\program_studi;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Illuminate\Http\Request;

class LaporanIkuController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Target Capaian';
        $q = $request->query('q');
        $tahunId = $request->query('tahun');

        $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();

        $query = target_indikator::select('target_indikator.*', 'indikator_kinerja.ik_nama', 'program_studi.nama_prodi', 'aktif_tahun.th_tahun')
            ->leftjoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftjoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftjoin('tahun_kerja as aktif_tahun', function ($join) {
                $join->on('aktif_tahun.th_id', '=', 'target_indikator.th_id')
                    ->where('aktif_tahun.ren_is_aktif', 'y');
            })
            ->orderBy('ti_target', 'asc');

        if ($q) {
            $query->where('ik_nama', 'like', '%' . $q . '%');
        }

        if ($tahunId) {
            $query->where('aktif_tahun.th_id', $tahunId);
        }

        $target_capaians = $query->paginate(10)->withQueryString();
        $no = $target_capaians->firstItem();

        return view('pages.index-laporan-iku', [
            'title' => $title,
            'target_capaians' => $target_capaians,
            'tahuns' => $tahuns,
            'tahunId' => $tahunId,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'laporan',
            'sub_menu' => 'laporan-iku',
        ]);
    }
}
