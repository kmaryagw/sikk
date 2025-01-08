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
        $title = 'Data Setting IKU';
        $q = $request->query('q');

        $settings = SettingIKU::with(['IndikatorKinerja'])
            ->whereHas('IndikatorKinerja', function ($query) use ($q) {
                $query->whereHas('tahun_kerja', function ($query) use ($q) {
                    $query->where('ik_nama', 'like', '%' . $q . '%');
                });
            })
            ->paginate(10);

        $no = $settings->firstItem();

        $ikus = IndikatorKinerja::orderBy('ik_nama', 'asc')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->orderBy('th_tahun', 'asc')->get();


        return view('pages.index-setting', [
            'title' => $title,
            'settings' => $settings,
            'ikus' => $ikus,
            'tahuns' => $tahuns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'SettingIKU',
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'ik_id' => 'required|exists:IndikatorKinerja,ik_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
        ]);

        try {
            $IndikatorKinerja = IndikatorKinerja::where('ik_id', $request->ik_id)->first();

            if (!$IndikatorKinerja) {
                return response()->json([
                    'success' => false,
                    'message' => 'IKU ini tidak memiliki target indikator.',
                ]);
            }

            $tahun = IndikatorKinerja::where('th_id', $request->th_id)->first();

            if (!$tahun) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun ini tidak memiliki target indikator.',
                ]);
            }

            $existingSetting = SettingIKU::where('ik_id', $request->ik_id)
                ->where('th_id', $request->th_id)
                ->first();

            if ($existingSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'IKU ini sudah ada untuk tahun yang sama.',
                ]);
            }

            $setting = SettingIKU::create([
                'id_setting' => 'ST' . md5(uniqid(rand(), true)),
                'ik_id' => $request->prodi_id,
                'th_id' => $request->th_id,
                'status' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Evaluasi berhasil disimpan!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ]);
        }
    }
}
