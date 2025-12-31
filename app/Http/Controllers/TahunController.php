<?php

namespace App\Http\Controllers;

use App\Models\Renstra;
use App\Models\tahun_kerja;
use App\Models\IkBaselineTahun;
use App\Models\MonitoringIKU;
use App\Models\MonitoringIKU_Detail;
use App\Models\program_studi;
use App\Models\IndikatorKinerja;
use App\Models\HistoryMonitoringIKU; 
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class TahunController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }
    
    public function index(Request $request)
    {
        $title = 'Data Tahun Kerja';
        $q = $request->query('q');
        
        $tahuns = tahun_kerja::where('th_tahun', 'like', '%' . $q . '%')
            ->leftJoin('renstra', 'renstra.ren_id', '=', 'tahun_kerja.ren_id')
            ->select('tahun_kerja.*', 'renstra.ren_nama')
            ->orderBy('th_tahun', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('pages.index-tahun', [
            'title' => $title,
            'tahuns' => $tahuns,
            'q' => $q,
            'no' => $tahuns->firstItem(),
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function create()
    {
        return view('pages.create-tahun', [
            'title' => 'Tambah Tahun',
            'th_is_aktifs' => ['y', 'n'],
            'renstras' => Renstra::orderBy('ren_nama', 'asc')->get(),
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'th_tahun' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'th_is_aktif' => 'required|in:y,n',
            'ren_id' => 'required',
        ]);

        $th_id = 'TH' . strtoupper(md5(time()));

        DB::transaction(function () use ($request, $th_id) {
            if ($request->th_is_aktif == 'y') {
                tahun_kerja::where('th_is_aktif', 'y')->update(['th_is_aktif' => 'n']);
            }

            $tahun = tahun_kerja::create([
                'th_id' => $th_id,
                'th_tahun' => $request->th_tahun,
                'ren_id' => $request->ren_id,
                'th_is_aktif' => $request->th_is_aktif,
                'th_is_editable' => 1
            ]);

            if ($tahun->th_is_aktif == 'y') {
                $this->copyCapaianToBaseline($tahun->th_id);
            }
        });

        Alert::success('Sukses', 'Data Berhasil Ditambah');
        return redirect()->route('tahun.index');
    }

    public function edit(tahun_kerja $tahun)
    {
        return view('pages.edit-tahun', [
            'title' => 'Ubah tahun',
            'th_is_aktifs' => ['y', 'n'],
            'renstras' => Renstra::orderBy('ren_nama', 'asc')->get(),
            'tahun' => $tahun,
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function update(tahun_kerja $tahun, Request $request)
    {
        $request->validate([
            'th_tahun' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'th_is_aktif' => 'required|in:y,n',
            'ren_id' => 'required',
        ]);

        if ($request->th_is_aktif === 'y') {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->where('th_id', '!=', $tahun->th_id)->first();
            if ($tahunAktif) {
                [$awalAktif] = explode('/', $tahunAktif->th_tahun);
                [$awalBaru] = explode('/', $request->th_tahun);
                $selisih = abs((int)$awalBaru - (int)$awalAktif);

                if ($selisih > 1) {
                    return back()->withErrors(['th_tahun' => 'Tahun aktif hanya boleh berurutan (Selisih 1 tahun).'])->withInput();
                }
            }
        }

        DB::transaction(function () use ($tahun, $request) {
            if ($request->th_is_aktif == 'y') {
                tahun_kerja::where('th_is_aktif', 'y')->where('th_id', '!=', $tahun->th_id)->update(['th_is_aktif' => 'n']);
            }

            $tahun->update([
                'th_tahun' => $request->th_tahun,
                'ren_id' => $request->ren_id,
                'th_is_aktif' => $request->th_is_aktif,
            ]);

            if ($tahun->th_is_aktif == 'y') {
                $this->copyCapaianToBaseline($tahun->th_id);
            }
        });

        Alert::success('Sukses', 'Data Berhasil Diubah');
        return redirect()->route('tahun.index');
    }

    /**
     * LOGIKA INTI: Salin Capaian Tahun Lalu ke Baseline Tahun Baru
     */
    private function copyCapaianToBaseline($newThId)
    {
        $allYears = tahun_kerja::orderBy('th_tahun', 'asc')->get();
        $currentIndex = $allYears->search(fn($t) => $t->th_id == $newThId);

        if ($currentIndex === false || $currentIndex === 0) {
            $this->setDefaultBaselineForNewYear($newThId);
            return;
        }

        $prevYear = $allYears->get($currentIndex - 1);
        $indikatorList = IndikatorKinerja::all();
        $prodiList = program_studi::all();
        $tahunTargetStr = $allYears->get($currentIndex)->th_tahun;

        // 1. Ambil Data Capaian Tahun Lalu dengan LEFT JOIN agar lebih aman
        // Menggunakan subquery/orderBy untuk memastikan mengambil yang terbaru jika ada duplikat
        $capaianTahunLalu = DB::table('monitoring_iku_detail as mid')
            ->join('monitoring_iku as mi', 'mid.mti_id', '=', 'mi.mti_id')
            ->join('target_indikator as ti', 'mid.ti_id', '=', 'ti.ti_id')
            ->where('mi.th_id', $prevYear->th_id)
            ->select('ti.ik_id', 'mi.prodi_id', 'mid.mtid_capaian')
            ->orderBy('mid.updated_at', 'desc')
            ->get()
            ->groupBy(['prodi_id', 'ik_id']);

        DB::transaction(function () use ($newThId, $tahunTargetStr, $indikatorList, $prodiList, $capaianTahunLalu) {
            foreach ($prodiList as $prodi) {
                foreach ($indikatorList as $indikator) {
                    
                    $dataCapaian = $capaianTahunLalu->get($prodi->prodi_id)?->get($indikator->ik_id)?->first();
                    $capaianValue = $dataCapaian ? $dataCapaian->mtid_capaian : null;

                    if (is_null($capaianValue) || $capaianValue === '') {
                        $ketercapaian = strtolower(trim($indikator->ik_ketercapaian));
                        if ($ketercapaian === 'rasio') $capaianValue = '0:0';
                        elseif (in_array($ketercapaian, ['nilai', 'persentase'])) $capaianValue = '0';
                        else $capaianValue = 'Draft';
                    }

                    DB::table('ik_baseline_tahun')->updateOrInsert(
                        [
                            'ik_id'    => $indikator->ik_id,
                            'th_id'    => $newThId,
                            'prodi_id' => $prodi->prodi_id,
                        ],
                        [
                            'baseline'   => $capaianValue,
                            'th_tahun'   => $tahunTargetStr,
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
        $tahun = tahun_kerja::find($newThId);
        $thTahunStr = $tahun ? $tahun->th_tahun : null;

        DB::transaction(function () use ($newThId, $indikatorList, $prodiList, $thTahunStr) {
            foreach ($indikatorList as $indikator) {
                $ketercapaian = strtolower(trim($indikator->ik_ketercapaian));
                $val = ($ketercapaian === 'rasio') ? '0:0' : (in_array($ketercapaian, ['nilai', 'persentase']) ? '0' : 'Draft');

                foreach ($prodiList as $prodi) {
                    DB::table('ik_baseline_tahun')->updateOrInsert(
                        ['ik_id' => $indikator->ik_id, 'th_id' => $newThId, 'prodi_id' => $prodi->prodi_id],
                        [
                            'baseline' => $val, 
                            'th_tahun' => $thTahunStr,
                            'updated_at' => now(),
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
        $tahun->th_is_editable = !$tahun->th_is_editable;
        $tahun->save();

        Alert::success('Sukses', "Data Tahun {$tahun->th_tahun} " . ($tahun->th_is_editable ? 'Dibuka' : 'Dikunci'));
        return redirect()->back();
    }

    public function destroy(tahun_kerja $tahun)
    {
        $tahun->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('tahun.index');
    }
}