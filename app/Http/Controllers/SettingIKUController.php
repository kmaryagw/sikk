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
        $indikatorKinerjas = IndikatorKinerja::orderBy('ik_nama', 'asc')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->orderBy('th_tahun', 'asc')->get();
        $q = $request->input('q', '');

        $settings = SettingIKU::with(['indikatorKinerja', 'tahunKerja'])
            ->join('indikator_kinerja', 'settingiku.ik_id', '=', 'indikator_kinerja.ik_id') // Sesuaikan foreign key jika berbeda
            ->when($q, function ($query) use ($q) {
                return $query->where('indikator_kinerja.ik_nama', 'like', "%$q%");
            })
            ->orderBy('indikator_kinerja.ik_nama', 'asc')
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
    // Validasi inputan
    $request->validate([
        'ik_id' => 'required|exists:indikator_kinerja,ik_id',
        'th_id' => 'required|exists:tahun_kerja,th_id',
        'baseline' => 'required|string',  // Pastikan baseline adalah angka
    ]);

    // Ambil data indikator kinerja berdasarkan ik_id
    $indikatorKinerja = IndikatorKinerja::find($request->ik_id);

    // Cek jika indikator kinerja ada dan memiliki tipe "persentase"
    if ($indikatorKinerja && $indikatorKinerja->ik_ketercapaian == 'persentase') {
        // Validasi bahwa baseline berada di antara 0 dan 100 untuk tipe persentase
        if ($request->baseline < 0 || $request->baseline > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Nilai baseline harus antara 0 dan 100 untuk indikator persentase.',
            ]);
        }
    }

    // Cek jika indikator kinerja memiliki tipe "nilai"
    if ($indikatorKinerja && $indikatorKinerja->ik_ketercapaian == 'nilai') {
        // Validasi bahwa baseline adalah angka positif
        if ($request->baseline <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Nilai baseline harus lebih besar dari 0 untuk indikator nilai.',
            ]);
        }
    }

    // Cek jika SettingIKU sudah ada untuk indikator dan tahun yang sama
    $Setting = SettingIKU::where('ik_id', $request->ik_id)
                         ->where('th_id', $request->th_id)
                         ->first();

    if ($Setting) {
        return response()->json([
            'success' => false,
            'message' => 'Indikator ini sudah ada untuk tahun yang sama.',
        ]);
    }

    // Menyimpan data SettingIKU baru
    SettingIKU::create([
        'id_setting' => 'IS' . md5(uniqid(rand(), true)), // ID unik untuk SettingIKU
        'ik_id' => $request->ik_id,
        'th_id' => $request->th_id,
        'baseline' => $request->baseline,
        'status' => 0,
    ]);

    return response()->json(['success' => true, 'message' => 'Setting IKU berhasil ditambahkan.']);
}



    public function update(Request $request, $id_setting)
    {
        $request->validate([
            'ik_id' => 'required|exists:indikator_kinerja,ik_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'baseline' => 'required|string',
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
            'baseline' => $request->baseline,
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