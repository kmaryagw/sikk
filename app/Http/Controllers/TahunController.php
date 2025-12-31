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
                'regex:/^\d{4}\/\d{4}$/', 
            ],
            'th_is_aktif' => 'required|in:y,n',
            'ren_id' => 'required',
        ]);

        $customPrefix = 'TH';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $th_id = $customPrefix . strtoupper($md5Hash);

        if ($request->th_is_aktif == 'y') {
            tahun_kerja::where('th_is_aktif', 'y')->update(['th_is_aktif' => 'n']);
        }

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
        $allYears = tahun_kerja::orderBy('th_tahun', 'asc')->get();
        $currentYearIndex = $allYears->search(fn($t) => $t->th_id == $newThId);

        if ($currentYearIndex === false || $currentYearIndex === 0) {
            $this->setDefaultBaselineForNewYear($newThId);
            return;
        }

        $prevYear = $allYears->get($currentYearIndex - 1);
        $indikatorList = IndikatorKinerja::all();
        $prodiList = program_studi::all();

        // 1. Ambil SEMUA capaian tahun lalu dalam SATU query (Sangat Efisien)
        $capaianTahunLalu = DB::table('monitoring_iku_detail as mid')
            ->join('monitoring_iku as mi', 'mid.mti_id', '=', 'mi.mti_id')
            ->join('target_indikator as ti', 'mid.ti_id', '=', 'ti.ti_id')
            ->where('mi.th_id', $prevYear->th_id)
            ->select('ti.ik_id', 'mi.prodi_id', 'mid.mtid_capaian')
            ->get()
            ->groupBy(['prodi_id', 'ik_id']); // Dikelompokkan agar mudah dicari

        DB::transaction(function () use ($newThId, $indikatorList, $prodiList, $capaianTahunLalu) {
            foreach ($prodiList as $prodi) {
                foreach ($indikatorList as $indikator) {
                    
                    // Cari apakah ada capaian untuk prodi & indikator ini di tahun lalu
                    $capaian = $capaianTahunLalu->get($prodi->prodi_id)?->get($indikator->ik_id)?->first();
                    
                    $baseline = $capaian ? $capaian->mtid_capaian : null;

                    // Jika tidak ditemukan capaian, berikan nilai default berdasarkan jenis ketercapaian
                    if ($baseline === null || $baseline === '') {
                        $ketercapaian = strtolower(trim($indikator->ik_ketercapaian));
                        if ($ketercapaian === 'rasio') $baseline = '0:0';
                        elseif (in_array($ketercapaian, ['nilai', 'persentase'])) $baseline = '0';
                        else $baseline = 'Draft';
                    }

                    // Gunakan updateOrInsert untuk performa database yang lebih baik
                    DB::table('ik_baseline_tahun')->updateOrInsert(
                        [
                            'ik_id'    => $indikator->ik_id,
                            'th_id'    => $newThId,
                            'prodi_id' => $prodi->prodi_id,
                        ],
                        [
                            'baseline'   => $baseline,
                            'th_tahun'   => tahun_kerja::find($newThId)->th_tahun ?? null,
                            'updated_at' => now(),
                            'created_at' => DB::raw('IFNULL(created_at, NOW())')
                        ]
                    );
                }
            }
        });
    }

    private function setDefaultBaselineForNewYear($newThId) 
    {
        $indikatorList = IndikatorKinerja::all();
        $prodiList = program_studi::all();
        
        // Ambil string tahunnya (misal 2024/2025) untuk disimpan di kolom th_tahun
        $tahun = tahun_kerja::find($newThId);
        $thTahunStr = $tahun ? $tahun->th_tahun : null;

        DB::transaction(function () use ($newThId, $indikatorList, $prodiList, $thTahunStr) {
            foreach ($indikatorList as $indikator) {
                
                // 1. Tentukan nilai default sekali saja per indikator (bukan di dalam loop prodi)
                $ketercapaian = strtolower(trim($indikator->ik_ketercapaian));
                if ($ketercapaian === 'rasio') {
                    $defaultVal = '0:0';
                } elseif (in_array($ketercapaian, ['nilai', 'persentase'])) {
                    $defaultVal = '0';
                } else {
                    $defaultVal = 'Draft';
                }

                foreach ($prodiList as $prodi) {
                    // 2. Gunakan updateOrInsert agar cepat dan memanfaatkan Unique Index yang sudah ada
                    DB::table('ik_baseline_tahun')->updateOrInsert(
                        [
                            'ik_id'    => $indikator->ik_id,
                            'th_id'    => $newThId,
                            'prodi_id' => $prodi->prodi_id,
                        ],
                        [
                            'baseline'   => $defaultVal,
                            'th_tahun'   => $thTahunStr,
                            'updated_at' => now(),
                            // created_at diisi NOW() hanya jika data baru (insert)
                            'created_at' => DB::raw('IFNULL(created_at, NOW())')
                        ]
                    );
                }
            }
        });
    }

    public function toggleLock($id)
    {
        $tahun = tahun_kerja::findOrFail($id);
        
        // Ubah status kebalikan dari sekarang (1 jadi 0, 0 jadi 1)
        $tahun->th_is_editable = !$tahun->th_is_editable;
        $tahun->save();

        $statusMsg = $tahun->th_is_editable ? 'Dibuka kembali' : 'Dikunci';
        
        Alert::success('Sukses', "Data Tahun {$tahun->th_tahun} berhasil {$statusMsg}");
        return redirect()->back();
    }

    public function destroy(tahun_kerja $tahun)
    {
        $tahun->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('tahun.index');
    }

}
