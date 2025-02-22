<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratNomor;
use App\Models\OrganisasiJabatan;
use App\Models\SuratKlasifikasiLingkup;
use App\Models\UnitKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class NomorSuratController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

            if ($user->role !== 'unit kerja') {
                abort(403, 'Unauthorized action.');
            }
    
        $title = 'Data Nomor Surat';
        $q = $request->query('q');
        $suratNomors = SuratNomor::with(['unitKerja', 'lingkup', 'organisasiJabatan', 'OrganisasiJabatan.parent.parent', 'lingkup.perihal', 'lingkup.perihal.fungsi'])
            ->where('sn_perihal', 'like', '%' . $q . '%')
            ->where('unit_id', $user->unit_id)
            ->orderByRaw("STR_TO_DATE(sn_tanggal, '%Y-%m-%d') ASC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(sn_nomor, '/', 1) AS UNSIGNED) ASC")
            ->paginate(10)
            ->withQueryString();
            // ->get();
    
        return view('pages.index-nomor-surat', [
            'title' => $title,
            'suratNomors' => $suratNomors,
            'q' => $q,
            'type_menu' => 'nomorsurat',
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'unit kerja') {
            abort(403, 'Unauthorized action.');
        }

        $title = 'Tambah Nomor Surat';
        $organisasiJabatans = OrganisasiJabatan::where('oj_mengeluarkan_nomor', 'y')
            ->with('parent.parent')
            ->get();
        $units = UnitKerja::where('unit_kerja', 'y')->get();        
        $lingkups = SuratKlasifikasiLingkup::with(['perihal', 'perihal.fungsi'])
            ->where('skl_aktif', 'y')
            ->get();

        return view('pages.create-nomor-surat', [
            'title' => $title,
            'organisasiJabatans' => $organisasiJabatans,
            'lingkups' => $lingkups,
            'units' => $units,
            'type_menu' => 'nomorsurat',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'oj_id' => 'required',
            'skl_id' => 'required',
            'sn_perihal' => 'required',
            'sn_keterangan' => 'required',
            'sn_tanggal' => 'required',
        ]);

        $snTanggal = Carbon::parse($request->sn_tanggal);

        $latestNomor = SuratNomor::where('oj_id', $request->oj_id)
                                ->where('skl_id', $request->skl_id)
                                ->whereMonth('sn_tanggal', $snTanggal->month)
                                ->whereYear('sn_tanggal', $snTanggal->year)
                                ->latest()
                                ->first();

        $nomorUrut = $latestNomor ? str_pad((int)$latestNomor->sn_nomor_urut + 1, 3, '0', STR_PAD_LEFT) : '001';
    
        $customPrefix = 'SN';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $sn_id = $customPrefix . strtoupper($md5Hash);

        $userUnitId = Auth::user()->unit_id;
    
        SuratNomor::create([
            'sn_id' => $sn_id,
            'unit_id' => $userUnitId,
            'skl_id' => $request->skl_id,
            'oj_id' => $request->oj_id,
            'sn_nomor_urut' => $nomorUrut,
            'sn_nomor' => null,
            'sn_tanggal' => $snTanggal,
            'sn_perihal' => $request->sn_perihal,
            'sn_keterangan' => $request->sn_keterangan,
            'sn_status' => 'draft',
        ]);
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
        return redirect()->route('nomorsurat.index');
    }

    public function edit($id)
    {
        $user = Auth::user();

        if ($user->role !== 'unit kerja') {
            abort(403, 'Unauthorized action.');
        }

        $nomorSurat = SuratNomor::findOrFail($id);
        $title = 'Edit Nomor Surat';
    
        $organisasiJabatans = OrganisasiJabatan::where('oj_mengeluarkan_nomor', 'y')->with('parent.parent')->get();
        $units = UnitKerja::where('unit_kerja', 'y')->get();
        $lingkups = SuratKlasifikasiLingkup::with(['perihal', 'perihal.fungsi'])->where('skl_aktif', 'y')->get();

        return view('pages.edit-nomor-surat', [
            'title' => $title,
            'nomorSurat' => $nomorSurat,
            'organisasiJabatans' => $organisasiJabatans,
            'lingkups' => $lingkups,
            'units' => $units,
            'type_menu' => 'nomorsurat',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'oj_id' => 'required',
            'skl_id' => 'required',
            'sn_perihal' => 'required',
            'sn_keterangan' => 'required',
            'sn_tanggal' => 'required',
        ]);

        $suratNomor = SuratNomor::findOrFail($id);
        $snTanggal = Carbon::parse($request->sn_tanggal);

        if ($suratNomor->oj_id != $request->oj_id || $suratNomor->skl_id != $request->skl_id || $suratNomor->sn_tanggal != $snTanggal) {
            $latestNomor = SuratNomor::where('oj_id', $request->oj_id)
                ->where('skl_id', $request->skl_id)
                ->whereMonth('sn_tanggal', $snTanggal->month)
                ->whereYear('sn_tanggal', $snTanggal->year)
                ->latest()
                ->first();

            $nomorUrut = $latestNomor ? str_pad((int)$latestNomor->sn_nomor_urut + 1, 3, '0', STR_PAD_LEFT) : '001';
        } else {
            $nomorUrut = $suratNomor->sn_nomor_urut;
        }

        $suratNomor->update([
            'oj_id' => $request->oj_id,
            'skl_id' => $request->skl_id,
            'sn_nomor_urut' => $nomorUrut,
            'sn_nomor' => null,
            'sn_tanggal' => $snTanggal,
            'sn_perihal' => $request->sn_perihal,
            'sn_keterangan' => $request->sn_keterangan,
        ]);

        Alert::success('Sukses', 'Data Berhasil Diperbarui');
        return redirect()->route('nomorsurat.index');
    }

    public function destroy($id)
    {
        $suratNomor = SuratNomor::findOrFail($id);
        $suratNomor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.'
        ]);
    }

    protected function generateNomorSurat($suratNomor)
    {
        $snTanggal = Carbon::parse($suratNomor->sn_tanggal);
        $bulanTahun = $snTanggal->format('m.Y');

        $oj = OrganisasiJabatan::find($suratNomor->oj_id);
        $skl = SuratKlasifikasiLingkup::with('perihal')->find($suratNomor->skl_id);
        $skpKode = $skl->perihal->skp_kode ?? '';

        $nomorUrut = str_pad($suratNomor->sn_nomor_urut, 3, '0', STR_PAD_LEFT);
    
        return "$nomorUrut/{$oj->oj_kode}/$skpKode.{$skl->skl_kode}/$bulanTahun";
    }

    public function validateSurat($id)
    {
        $suratNomor = SuratNomor::findOrFail($id);

        if ($suratNomor->sn_status == 'draft' || $suratNomor->sn_status == 'revisi') {
            $nomorSurat = $this->generateNomorSurat($suratNomor);
            $suratNomor->update([
                'sn_nomor' => $nomorSurat,
                'sn_status' => 'validasi'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil divalidasi.'
        ]);
    }

    public function ajukanData($id)
    {
        $suratNomor = SuratNomor::findOrFail($id);
        if ($suratNomor->sn_status == 'draft') {
            $suratNomor->update(['sn_status' => 'ajukan']);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diajukan.'
        ]);
    }

    // public function indexAjukan(Request $request)
    // {
    //     $user = Auth::user();

    //         if ($user->role !== 'admin') {
    //             abort(403, 'Unauthorized action.');
    //         }
    
    //     $title = 'Data Nomor Surat yang perlu ditinjau';
    //     $q = $request->query('q');
    //     $ajukans = SuratNomor::with(['unitKerja', 'lingkup', 'organisasiJabatan', 'OrganisasiJabatan.parent'])
    //         ->where('sn_status', 'ajukan')
    //         ->orderByRaw("STR_TO_DATE(sn_tanggal, '%Y-%m-%d') ASC")
    //         ->orderByRaw("CAST(SUBSTRING_INDEX(sn_nomor, '/', 1) AS UNSIGNED) ASC")
    //         ->get();
    
    //     return view('pages.index-menunggu-validasi', [
    //         'title' => $title,
    //         'ajukans' => $ajukans,
    //         'q' => $q,
    //         'type_menu' => 'surat',
    //         'sub_menu' => 'menungguvalidasi',
    //     ]);
    // }

    // public function validateSuratAdmin($id)
    // {
    //     $suratNomor = SuratNomor::findOrFail($id);

    //     if ($suratNomor->sn_status == 'ajukan') {
    //         $nomorSurat = $this->generateNomorSurat($suratNomor);
    //         $suratNomor->update([
    //             'sn_nomor' => $nomorSurat,
    //             'sn_status' => 'validasi'
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data berhasil divalidasi.'
    //     ]);
    // }
}