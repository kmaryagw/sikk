<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\PeriodeMonitoring;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Monitoring ';
        $q = $request->query('q');

        $periodemonitorings = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')
            ->join('periode_monev', 'periode_monitoring.pm_id', '=', 'periode_monev.pm_id')
            ->orderBy('periode_monev.pm_nama', 'asc')
            ->where('th_id', 'like', '%' . $q . '%')
            ->paginate(10);

        $no = $periodemonitorings->firstItem();

        foreach ($periodemonitorings as $item) {
            $tanggalMulai = Carbon::parse($item->pmo_tanggal_mulai);
            $tanggalSelesai = Carbon::parse($item->pmo_tanggal_selesai);

            $selisihBulan = $tanggalMulai->diffInMonths($tanggalSelesai);

            $item->is_within_three_months = $selisihBulan <= 3;
        }

        return view('pages.index-monitoring', [
            'title' => $title,
            'periodemonitorings' => $periodemonitorings,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'monitoring',
        ]);
    }

    

    public function show($pmo_id)
{
    $periodemonitoring = PeriodeMonitoring::with('tahunKerja', 'periodes')
        ->findOrFail($pmo_id);

    // Mendapatkan program kerja yang terkait dengan periode monitoring
    $programKerjas = RencanaKerja::whereHas('periodes', function ($query) use ($periodemonitoring) {
        $query->where('rencana_kerja_pelaksanaan.pm_id', $periodemonitoring->pm_id);
    })->with(['unitKerja', 'monitoring'])->get();

    return view('pages.monitoring-show', [
        'periodemonitoring' => $periodemonitoring,
        'programKerjas' => $programKerjas,
        'type_menu' => 'monitoring',
    ]);
}


public function fill($pmo_id)
{
    $periodeMonitoring = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')->findOrFail($pmo_id);

    $rencanaKerja = RencanaKerja::with(['monitoring' => function ($query) use ($pmo_id) {
        $query->where('pmo_id', $pmo_id);
    }, 'unitKerja'])->whereHas('periodes', function ($query) use ($periodeMonitoring) {
        $query->where('rencana_kerja_pelaksanaan.pm_id', $periodeMonitoring->pm_id);
    })->get();

    $realisasi = RealisasiRenja::whereIn('rk_id', $rencanaKerja->pluck('rk_id'))->get()->keyBy('rk_id');

    // Menentukan apakah data monitoring sudah diisi
    foreach ($rencanaKerja as $rencana) {
        $rencana->is_submitted = $rencana->monitoring->isNotEmpty();
    }

    return view('pages.monitoring-fill', [
        'realisasi' => $realisasi,
        'periodeMonitoring' => $periodeMonitoring,
        'rencanaKerja' => $rencanaKerja,
        'type_menu' => 'monitoring',
    ]);
}





public function store(Request $request)
{
    // Validasi data yang diterima
    $validated = $request->validate([
        'mtg_capaian' => 'required|numeric',
        'mtg_kondisi' => 'required|string',
        'mtg_kendala' => 'nullable|string',
        'mtg_tindak_lanjut' => 'nullable|string',
        'mtg_tindak_lanjut_tanggal' => 'nullable|date',
        'mtg_bukti' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
    ]);

    try {
        // Ambil data monitoring berdasarkan pmo_id dan rk_id, atau buat baru
        $monitoring = Monitoring::firstOrNew(['pmo_id' => $request->pmo_id, 'rk_id' => $request->rk_id]);

        // Update data monitoring dengan nilai baru
        $monitoring->mtg_capaian = $validated['mtg_capaian'];
        $monitoring->mtg_kondisi = $validated['mtg_kondisi'];
        $monitoring->mtg_kendala = $validated['mtg_kendala'];
        $monitoring->mtg_tindak_lanjut = $validated['mtg_tindak_lanjut'];
        $monitoring->mtg_tindak_lanjut_tanggal = $validated['mtg_tindak_lanjut_tanggal'];

        // Update mtg_bukti jika ada file baru yang diunggah
        if ($request->hasFile('mtg_bukti')) {
            // Hapus file lama jika ada
            if ($monitoring->mtg_bukti) {
                Storage::disk('public')->delete($monitoring->mtg_bukti);
            }
            // Simpan file baru
            $monitoring->mtg_bukti = $request->file('mtg_bukti')->store('monitoring_bukti', 'public');
        }

        // Simpan data monitoring
        $monitoring->save();

        Alert::success('Berhasil', 'Data monitoring berhasil disimpan.');
        return redirect()->route('monitoring.fill', ['pmo_id' => $request->pmo_id])
            ->with('success', 'Data monitoring berhasil disimpan.');
    } catch (\Exception $e) {
        Alert::error('Gagal', 'Terjadi kesalahan dalam menyimpan data.');
        return redirect()->route('monitoring.fill', ['pmo_id' => $request->pmo_id])
            ->with('error', 'Terjadi kesalahan dalam menyimpan data: ');

    }
}





public function getData($pmo_id, $rk_id)
{
    $monitoring = Monitoring::where('pmo_id', $pmo_id)->where('rk_id', $rk_id)->first();

    return response()->json($monitoring);
}


}
