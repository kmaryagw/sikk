<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\SettingIKU;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;

class SettingIKUController extends Controller
{
    public function index(Request $request)
{
    $title = 'Setting IKU';
    $indikatorKinerjas = IndikatorKinerja::all();
    $tahuns = tahun_kerja::where('th_is_aktif', 'y')->orderBy('th_tahun', 'asc')->get();
    $q = $request->input('q', '');

    // Logika pencarian pada relasi indikator_kinerja
    $settings = SettingIKU::with(['indikatorKinerja', 'tahunKerja'])
        ->when($q, function ($query) use ($q) {
            return $query->whereHas('indikatorKinerja', function ($query) use ($q) {
                $query->where('ik_nama', 'like', "%$q%");
            });
        })
        ->paginate(10);

    return view('pages.index-settingiku', [
        'title' => $title,
        'settings' => $settings,
        'indikatorKinerjas' => $indikatorKinerjas,
        'tahuns' => $tahuns,
        'q' => $q,
        'type_menu' => 'SettingIKU',
    ]);
}


    public function store(Request $request)
    {
        $request->validate([
            'ik_id' => 'required|exists:IndikatorKinerja,ik_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
        ]);

        SettingIKU::create([
            'ik_id' => $request->ik_id,
            'th_id' => $request->th_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Setting IKU berhasil ditambahkan.']);
    }

    public function destroy($id)
    {
        $setting = SettingIKU::findOrFail($id);
        $setting->delete();

        return redirect()->route('settingiku.index')->with('success', 'Setting IKU berhasil dihapus.');
    }
}
