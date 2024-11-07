<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
use Illuminate\Routing\Controller;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class RealisasiRenjaController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Realisasi Renja';
        $q = $request->query('q');

        $rencanaKerjas = RencanaKerja::with('tahunKerja', 'UnitKerja')
            ->where('rk_nama', 'like', '%' . $q . '%')
            ->orderBy('rk_nama', 'asc')
            ->paginate(10);
        $no = $rencanaKerjas->firstItem();

        return view('pages.index-realisasirenja', [
            'title' => $title,
            'rencanaKerjas' => $rencanaKerjas,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'realisasirenja',
        ]);
    }

    public function showRealisasi($rk_id)
{
    // Jika rk_id adalah string yang dipisahkan koma, ubah menjadi array
    $rk_id = explode(',', $rk_id);

    $rencanaKerja = RencanaKerja::findOrFail($rk_id[0]); // Ambil salah satu sebagai contoh
    $realisasi = RealisasiRenja::whereIn('rk_id', $rk_id)->orderBy('created_at', 'asc')->get();

    return view('pages.index-detail-realisasi', [
        'rencanaKerja' => $rencanaKerja,
        'realisasi' => $realisasi,
        'type_menu' => 'realisasirenja',
    ]);
}


    public function create(Request $request)
{
    $title = 'Tambah Realisasi Renja';

    // Mengambil data RencanaKerja berdasarkan rk_id dari request
    $rencanaKerja = RencanaKerja::with('periodes')->findOrFail($request->rk_id); 

    return view('pages.create-realisasirenja', [
        'title' => $title,
        'rencanaKerja' => $rencanaKerja,
        'rk_nama' => $rencanaKerja->rk_nama,
        'pm_nama' => optional($rencanaKerja->periode)->pm_nama, // Hanya jika `pm_nama` dari relasi 'periode'
        'type_menu' => 'realisasirenja',
    ]);
}


public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'rkr_url' => 'nullable|url',
        'rkr_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'rkr_deskripsi' => 'nullable|string',
        'rkr_capaian' => 'required|integer',
        'rkr_tanggal' => 'required|date',
        'pm_id' => 'nullable',
    ]);

    // Validasi bahwa setidaknya rkr_url atau rkr_deskripsi harus diisi
    if (empty($request->rkr_url) && empty($request->rkr_deskripsi)) {
        Alert::error('Error', 'URL atau Deskripsi harus diisi.');
        return back()->withInput();
    }

    // Menghasilkan rkr_id
    $customPrefix = 'RKR';
    $timestamp = time();
    $md5Hash = md5($timestamp);
    $rkr_id = $customPrefix . strtoupper($md5Hash);

    // Simpan Realisasi
    $realisasi = new RealisasiRenja();
    $realisasi->rkr_id = $rkr_id;
    $realisasi->rk_id = is_array($request->rk_id) ? implode(",", $request->rk_id) : $request->rk_id;
    $realisasi->rkr_url = $request->rkr_url;

    // Simpan file dengan storeAs jika ada
    if ($request->hasFile('rkr_file')) {
        $file = $request->file('rkr_file');
        $hashedFilename = Hash::make($file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();
        $filePath = $file->storeAs('realisasi_files', $hashedFilename . '.' . $extension, 'public');

        // Jika penyimpanan file gagal
        if (!$filePath) {
            Alert::error('Error', 'File Gagal Disimpan!');
            return back()->withInput();
        }

        $realisasi->rkr_file = $filePath;
    }

    $realisasi->rkr_deskripsi = $request->rkr_deskripsi;
    $realisasi->rkr_capaian = $request->rkr_capaian;
    $realisasi->rkr_tanggal = $request->rkr_tanggal;
    $realisasi->save();

    Alert::success('Sukses', 'Data Berhasil Ditambah');
    return redirect()->route('realisasirenja.showRealisasi', $realisasi->rk_id);
}

public function edit($id)
{
    $realisasi = RealisasiRenja::findOrFail($id);
    $rencanaKerja = RencanaKerja::with('periodes')->findOrFail($realisasi->rk_id); // Ambil Rencana Kerja

    return view('pages.edit-realisasirenja', [
        'title' => 'Edit Realisasi Renja',
        'realisasi' => $realisasi,
        'rencanaKerja' => $rencanaKerja,
        'rk_nama' => $rencanaKerja->rk_nama,
        'pm_nama' => optional($rencanaKerja->periode)->pm_nama,
        'type_menu' => 'realisasirenja',
    ]);
}

public function update(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'rkr_url' => 'nullable|url',
        'rkr_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'rkr_deskripsi' => 'nullable|string',
        'rkr_capaian' => 'required|integer',
        'rkr_tanggal' => 'required|date',
        'pm_id' => 'nullable',
    ]);

    // Validasi bahwa setidaknya rkr_url atau rkr_deskripsi harus diisi
    if (empty($request->rkr_url) && empty($request->rkr_deskripsi)) {
        Alert::error('Error', 'URL atau Deskripsi harus diisi.');
        return back()->withInput();
    }

    // Temukan Realisasi yang akan diupdate
    $realisasi = RealisasiRenja::findOrFail($id);

    // Update properti Realisasi
    $realisasi->rk_id = $request->rk_id; // Pastikan ini diisi dengan rk_id yang sesuai
    $realisasi->rkr_url = $request->rkr_url;

    // Jika ada file baru, simpan file dan update path
    if ($request->hasFile('rkr_file')) {
        $file = $request->file('rkr_file');
        $hashedFilename = Hash::make($file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();
        $filePath = $file->storeAs('realisasi_files', $hashedFilename . '.' . $extension, 'public');

        if (!$filePath) {
            Alert::error('Error', 'File Gagal Disimpan!');
            return back()->withInput();
        }

        if ($realisasi && Storage::exists('public/' . $realisasi->rkr_file)) {
            Storage::delete('public/' . $realisasi->rkr_file);
        }

        $realisasi->rkr_file = $filePath;
    }

    $realisasi->rkr_deskripsi = $request->rkr_deskripsi;
    $realisasi->rkr_capaian = $request->rkr_capaian;
    $realisasi->rkr_tanggal = $request->rkr_tanggal;
    $realisasi->save();

    Alert::success('Sukses', 'Data Berhasil Diperbarui');
    return redirect()->route('realisasirenja.showRealisasi', $realisasi->rk_id);
}

public function destroy($rkr_id)
{
    $realisasi = RealisasiRenja::find($rkr_id);

    if (!$realisasi) {
        return redirect()->back()->withErrors('Data tidak ditemukan.');
    }

    if ($realisasi->rkr_file && Storage::exists('public/' . $realisasi->rkr_file)) {
        Storage::delete('public/' . $realisasi->rkr_file);
    }

    $realisasi->delete();
    Alert::success('Sukses', 'Data Berhasil Dihapus');
    return redirect()->route('realisasirenja.showRealisasi', $realisasi->rk_id);
}

}