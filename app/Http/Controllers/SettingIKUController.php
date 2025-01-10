<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\SettingIKU;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

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
            'ik_id' => 'required|exists:indikator_kinerja,ik_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
        ]);

        $Setting = SettingIKU::where('ik_id', $request->ik_id)
                ->where('th_id', $request->th_id)
                ->first();

            if ($Setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Indikator ini sudah ada untuk tahun yang sama.',
                ]);
            }

        SettingIKU::create([
            'id_setting' => 'IS' . md5(uniqid(rand(), true)),
            'ik_id' => $request->ik_id,
            'th_id' => $request->th_id,
            'status' => 0,
        ]);

        return response()->json(['success' => true, 'message' => 'Setting IKU berhasil ditambahkan.']);
    }

    public function update(Request $request, $id_setting)
{
    $request->validate([
        'ik_id' => 'required|exists:indikator_kinerja,ik_id',
        'th_id' => 'required|exists:tahun_kerja,th_id',
    ]);

    $setting = SettingIKU::findOrFail($id_setting);
    $existingSetting = SettingIKU::where('ik_id', $request->ik_id)
        ->where('th_id', $request->th_id)
        ->where('id_setting', '!=', $id_setting)
        ->first();

    if ($existingSetting) {
        return response()->json([
            'success' => false,
            'message' => 'Indikator ini sudah ada untuk tahun yang sama.',
        ]);
    }

    $setting->update([
        'ik_id' => $request->ik_id,
        'th_id' => $request->th_id,
    ]);

    return response()->json(['success' => true, 'message' => 'Setting IKU berhasil diperbarui.']);
}


public function destroy($id_setting)
{
    $setting = SettingIKU::find($id_setting);

    if ($setting) {
        $setting->delete();

        Alert::success('Sukses', 'Data berhasil dihapus');
        return redirect()->route('settingiku.index');
    }

    Alert::error('Error', 'Terjadi kesalahan!');
    return redirect()->route('settingiku.index');
}


}
