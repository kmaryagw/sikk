<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\PeriodeMonitoring;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Monitoring';
        $q = $request->query('q');

        // Ambil data periode monitoring dengan pencarian
        $periode_monitoring = PeriodeMonitoring::with('rencanaKerja')
            ->whereHas('rencanaKerja', function ($query) use ($q) {
                $query->where('rk_nama', 'like', '%' . $q . '%');
            })
            ->orderBy('pmo_tanggal_mulai', 'asc')
            ->paginate(10);

        $no = $periode_monitoring->firstItem();

        return view('pages.index-monitoring', [
            'title' => $title,
            'periode_monitoring' => $periode_monitoring,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'monitoring',
        ]);
    }

    public function fill($pmo_id)
    {
        $monitoring = Monitoring::with('rencanaKerja')->findOrFail($pmo_id);
        return view('pages.monitoring-fill', compact('monitoring'));
    }

    public function view($pmo_id)
    {
        $monitoring = Monitoring::with('rencanaKerja')->findOrFail($pmo_id);
        return view('monitoring.view', compact('monitoring'));
    }

    public function edit($pmo_id)
    {
        $periode = PeriodeMonitoring::findOrFail($pmo_id);
        return view('pages.edit-periode-monitoring', compact('periode'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pmo_tanggal_mulai' => 'required|date',
            'pmo_tanggal_selesai' => 'required|date|after_or_equal:pmo_tanggal_mulai',
        ]);

        $periode = PeriodeMonitoring::findOrFail($id);
        $periode->pmo_tanggal_mulai = $request->pmo_tanggal_mulai;
        $periode->pmo_tanggal_selesai = $request->pmo_tanggal_selesai;
        $periode->save();

        Alert::success('Berhasil', 'Periode Monitoring berhasil diperbarui!');
        return redirect()->route('periode-monitoring.index');
    }
}
