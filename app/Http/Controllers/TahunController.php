<?php

namespace App\Http\Controllers;

use App\Models\Renstra;
use App\Models\tahun_kerja;
use App\Models\IkBaselineTahun;
use App\Models\MonitoringIKU;
use App\Models\MonitoringIKU_Detail;
use App\Models\program_studi;
use App\Models\IndikatorKinerja;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TahunController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $title = 'Data Tahun Kerja';
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $q = $request->query('q');
        $tahuns = tahun_kerja::where('th_tahun', 'like', '%' . $q . '%')
            ->orderBy('th_tahun', 'asc')
            ->leftjoin('renstra', 'renstra.ren_id', '=', 'tahun_kerja.ren_id')
            ->paginate(10)
            ->withQueryString();
        $no = $tahuns->firstItem();
        
        return view('pages.index-tahun', [
            'title' => $title,
            'tahuns' => $tahuns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function create()
    {
        $title = 'Tambah Tahun';
        
        $th_is_aktifs = ['y', 'n'];
        $renstras = Renstra::orderBy('ren_nama', 'asc')->get();

        return view('pages.create-tahun', [
            'title' => $title,
            'th_is_aktifs' => $th_is_aktifs,
            'renstras' => $renstras,
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'th_tahun' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/', // hanya format 4 angka / 4 angka, misal 2024/2025
            ],
            'th_is_aktif' => 'required|in:y,n',
            'ren_id' => 'required',
        ]);

        // Buat ID Tahun baru
        $customPrefix = 'TH';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $th_id = $customPrefix . strtoupper($md5Hash);

        // Jika ingin aktif, nonaktifkan yang lain dulu
        if ($request->th_is_aktif == 'y') {
            tahun_kerja::where('th_is_aktif', 'y')->update(['th_is_aktif' => 'n']);
        }

        // Simpan data tahun
        $tahun = new tahun_kerja();
        $tahun->th_id = $th_id;
        $tahun->th_tahun = $request->th_tahun;
        $tahun->ren_id = $request->ren_id;
        $tahun->th_is_aktif = $request->th_is_aktif;
        $tahun->save();

        // Cek: Jika tahun baru ini aktif → salin baseline
        if ($tahun->th_is_aktif == 'y') {
            $this->copyCapaianToBaseline($tahun->th_id);
        }

        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('tahun.index');
    }

    public function edit(tahun_kerja $tahun)
    {
        $title = 'Ubah tahun';
        $th_is_aktifs = ['y', 'n'];
        $renstras = Renstra::orderBy('ren_nama', 'asc')->get();
    
        return view('pages.edit-tahun', [
            'title' => $title,
            'th_is_aktifs' => $th_is_aktifs,
            'renstras' => $renstras,
            'tahun' => $tahun,
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function update(tahun_kerja $tahun, Request $request)
    {
        $request->validate([
            'th_tahun' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
            ],
            'th_is_aktif' => 'required|in:y,n',
            'ren_id' => 'required',
        ]);

        // VALIDASI TAHUN BERURUTAN
        if ($request->th_is_aktif === 'y') {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')
                ->where('th_id', '!=', $tahun->th_id)
                ->first();

            if ($tahunAktif) {
                [$awalAktif, $akhirAktif] = explode('/', $tahunAktif->th_tahun);
                [$awalBaru, $akhirBaru] = explode('/', $request->th_tahun);

                $selisihAwal = (int)$awalBaru - (int)$awalAktif;
                $selisihAkhir = (int)$akhirBaru - (int)$akhirAktif;

                if (!(($selisihAwal === 1 && $selisihAkhir === 1) || ($selisihAwal === -1 && $selisihAkhir === -1))) {
                    return back()->withErrors([
                        'th_tahun' => 'Tahun yang bisa diaktifkan hanya satu tingkat sebelum atau sesudah tahun aktif sekarang (' . $tahunAktif->th_tahun . ').'
                    ])->withInput();
                }
            }
        }

        // Nonaktifkan tahun lain jika tahun ini akan aktif
        if ($request->th_is_aktif == 'y') {
            tahun_kerja::where('th_is_aktif', 'y')
                ->where('th_id', '!=', $tahun->th_id)
                ->update(['th_is_aktif' => 'n']);
        }

        $tahun->th_tahun = $request->th_tahun;
        $tahun->ren_id = $request->ren_id;
        $tahun->th_is_aktif = $request->th_is_aktif;
        $tahun->save();

        if ($tahun->th_is_aktif == 'y') {
            $this->copyCapaianToBaseline($tahun->th_id);
        }

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('tahun.index');
    }


    private function copyCapaianToBaseline($newThId)
    {
        // Ambil semua tahun kerja, urutkan berdasar tahun awal (4 digit pertama dari th_tahun)
        $allYears = tahun_kerja::get()->sortBy(function ($t) {
            return (int) substr($t->th_tahun, 0, 4);
        })->values();

        // Tahun pertama
        $firstYear = $allYears->first();

        // Ambil semua indikator
        $indikatorList = IndikatorKinerja::all();

        // Ambil semua prodi
        $prodiList = program_studi::all();

        // === CASE 1: Tahun pertama ===
        if ($firstYear && $firstYear->th_id == $newThId) {
            foreach ($indikatorList as $indikator) {
                foreach ($prodiList as $prodi) {
                    IkBaselineTahun::updateOrCreate(
                        [
                            'ik_id'    => $indikator->ik_id,
                            'th_id'    => $newThId,
                            'prodi_id' => $prodi->prodi_id,
                        ],
                        [
                            'baseline' => $indikator->ik_baseline ?? 0
                        ]
                    );
                }
            }
            return;
        }

        // === CASE 2: Ada tahun sebelumnya ===
        $currentYearIndex = $allYears->search(fn($t) => $t->th_id == $newThId);

        if ($currentYearIndex === false || $currentYearIndex === 0) {
            return; // Tidak ada tahun sebelumnya
        }

        $prevYear = $allYears->get($currentYearIndex - 1);

        foreach ($indikatorList as $indikator) {
            foreach ($prodiList as $prodi) {
                $baseline = 0;

                $details = MonitoringIKU_Detail::select('monitoring_iku_detail.*')
                    ->join('target_indikator as ti', 'monitoring_iku_detail.ti_id', '=', 'ti.ti_id')
                    ->join('monitoring_iku as mi', 'monitoring_iku_detail.mti_id', '=', 'mi.mti_id')
                    ->where('mi.th_id', $prevYear->th_id)
                    ->where('ti.ik_id', $indikator->ik_id)
                    ->where('ti.prodi_id', $prodi->prodi_id) // filter per prodi
                    ->whereNotNull('monitoring_iku_detail.mtid_capaian')
                    ->get();

                if ($details->isNotEmpty()) {
                    $ketercapaian = strtolower(trim($indikator->ik_ketercapaian));

                    if (in_array($ketercapaian, ['persentase', 'nilai'])) {
                        $highest = $details->filter(fn($d) => is_numeric(trim($d->mtid_capaian)))
                            ->sortByDesc(fn($d) => (float)$d->mtid_capaian)
                            ->first();
                        if ($highest) {
                            $baseline = $highest->mtid_capaian;
                        }
                    } else {
                        $latest = $details->sortByDesc('created_at')->first();
                        if ($latest && trim($latest->mtid_capaian) !== '') {
                            $baseline = $latest->mtid_capaian;
                        }
                    }
                }

                IkBaselineTahun::updateOrCreate(
                    [
                        'ik_id'    => $indikator->ik_id,
                        'th_id'    => $newThId,
                        'prodi_id' => $prodi->prodi_id,
                    ],
                    [
                        'baseline' => $baseline
                    ]
                );
            }
        }
    }

    public function destroy(tahun_kerja $tahun)
    {
        $tahun->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('tahun.index');
    }

}
