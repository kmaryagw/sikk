<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\periode_monev;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Monitoring';
        $tahunId = $request->query('th_id');
        $periodeId = $request->query('pm_id');
        $unitId = Auth::user()->unit_id;

        // Mendapatkan data tahun dan periode untuk dropdown filter
        $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();
        $periodes = periode_monev::all();

        // Query data monitoring sesuai filter
        $monitoring = Monitoring::where('th_id', $tahunId)
            ->where('pm_id', $periodeId)
            ->whereHas('rencanaKerja', function($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })
            ->paginate(10);

        return view('pages.index-monitoring', compact('title', 'monitoring', 'tahuns', 'periodes', 'tahunId', 'periodeId'));
    }

    // Create - Menampilkan form untuk membuat data monitoring baru
    public function create()
    {
        $title = 'Tambah Monitoring';
        $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();
        $periodes = periode_monev::all();
        $rencanas = RencanaKerja::where('unit_id', Auth::user()->unit_id)->get();

        return view('pages.create-monitoring', compact('title', 'tahuns', 'periodes', 'rencanas'));
    }

    // Store - Menyimpan data monitoring baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'required|exists:periode_monev,pm_id',
            'rk_id' => 'required|exists:rencana_kerja,rk_id',
            'mtg_capaian' => 'required|string|max:255',
            'mtg_kondisi' => 'required|string|max:255',
            'mtg_kendala' => 'required|string|max:255',
            'mtg_tindak_lanjut' => 'required|string|max:255',
            'mtg_tindak_lanjut_tanggal' => 'required|date',
            'mtg_bukti' => 'nullable|string|max:255'
        ]);

        // Buat ID unik untuk monitoring
        $monitoring = new Monitoring();
        $monitoring->mtg_id = 'MTG' . strtoupper(md5(time()));
        $monitoring->th_id = $request->th_id;
        $monitoring->pm_id = $request->pm_id;
        $monitoring->rk_id = $request->rk_id;
        $monitoring->mtg_capaian = $request->mtg_capaian;
        $monitoring->mtg_kondisi = $request->mtg_kondisi;
        $monitoring->mtg_kendala = $request->mtg_kendala;
        $monitoring->mtg_tindak_lanjut = $request->mtg_tindak_lanjut;
        $monitoring->mtg_tindak_lanjut_tanggal = $request->mtg_tindak_lanjut_tanggal;
        $monitoring->mtg_bukti = $request->mtg_bukti;
        $monitoring->save();

        Alert::success('Sukses', 'Data Monitoring Berhasil Ditambah');
        return redirect()->route('monitoring.index');
    }

    public function edit(Monitoring $monitoring)
    {
        $title = 'Edit Monitoring';
        $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();
        $periodes = periode_monev::all();
        $rencanas = RencanaKerja::where('unit_id', Auth::user()->unit_id)->get();

        return view('pages.edit-monitoring', compact('title', 'monitoring', 'tahuns', 'periodes', 'rencanas'));
    }

    public function update(Request $request, Monitoring $monitoring)
    {
        $request->validate([
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'required|exists:periode_monev,pm_id',
            'rk_id' => 'required|exists:rencana_kerja,rk_id',
            'mtg_capaian' => 'required|string|max:255',
            'mtg_kondisi' => 'required|string|max:255',
            'mtg_kendala' => 'required|string|max:255',
            'mtg_tindak_lanjut' => 'required|string|max:255',
            'mtg_tindak_lanjut_tanggal' => 'required|date',
            'mtg_bukti' => 'nullable|string|max:255'
        ]);

        $monitoring->th_id = $request->th_id;
        $monitoring->pm_id = $request->pm_id;
        $monitoring->rk_id = $request->rk_id;
        $monitoring->mtg_capaian = $request->mtg_capaian;
        $monitoring->mtg_kondisi = $request->mtg_kondisi;
        $monitoring->mtg_kendala = $request->mtg_kendala;
        $monitoring->mtg_tindak_lanjut = $request->mtg_tindak_lanjut;
        $monitoring->mtg_tindak_lanjut_tanggal = $request->mtg_tindak_lanjut_tanggal;
        $monitoring->mtg_bukti = $request->mtg_bukti;
        $monitoring->save();

        Alert::success('Sukses', 'Data Monitoring Berhasil Diubah');
        return redirect()->route('monitoring.index');
    }

    public function destroy(Monitoring $monitoring)
    {
        $monitoring->delete();

        Alert::success('Sukses', 'Data Monitoring Berhasil Dihapus');
        return redirect()->route('monitoring.index');
    }


}
