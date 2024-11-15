<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\PeriodeMonitoring;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    // Menentukan apakah data monitoring sudah diisi
    foreach ($rencanaKerja as $rencana) {
        $rencana->is_submitted = $rencana->monitoring->isNotEmpty();
    }

    return view('pages.monitoring-fill', [
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
        // Proses penyimpanan data monitoring
        $monitoring = Monitoring::updateOrCreate(
            ['pmo_id' => $request->pmo_id, 'rk_id' => $request->rk_id],
            [
                'mtg_capaian' => $validated['mtg_capaian'],
                'mtg_kondisi' => $validated['mtg_kondisi'],
                'mtg_kendala' => $validated['mtg_kendala'],
                'mtg_tindak_lanjut' => $validated['mtg_tindak_lanjut'],
                'mtg_tindak_lanjut_tanggal' => $validated['mtg_tindak_lanjut_tanggal'],
            ]
        );

        // Simpan file dengan storeAs jika ada file
        if ($request->hasFile('mtg_bukti')) {
            $file = $request->file('mtg_bukti');
            $hashedFilename = md5($file->getClientOriginalName() . time()); // Hash dengan timestamp
            $extension = $file->getClientOriginalExtension();
            $filePath = $file->storeAs('monitoring_bukti', $hashedFilename . '.' . $extension, 'public');

            // Update kolom mtg_bukti dengan path file
            $monitoring->mtg_bukti = $filePath;
            $monitoring->save();

            // Jika file gagal disimpan
            if (!$filePath) {
                Alert::error('Error', 'File Gagal Disimpan!');
                return back();
            }
        }

        Alert::success('Berhasil', 'Data monitoring berhasil disimpan.');
        return redirect()->route('monitoring.index')->with('success', 'Data monitoring berhasil disimpan.');
    } catch (\Exception $e) {
        // Tangkap error dan kembalikan respon gagal
        Alert::error('Gagal', 'Terjadi kesalahan dalam menyimpan data.');
        return redirect()->route('monitoring.index')->with('error', 'Terjadi kesalahan dalam menyimpan data.');
    }
}

public function getData($pmo_id, $rk_id)
{
    $monitoring = Monitoring::where('pmo_id', $pmo_id)->where('rk_id', $rk_id)->first();

    return response()->json($monitoring);
}


}
