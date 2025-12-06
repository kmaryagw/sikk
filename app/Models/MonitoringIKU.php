<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\target_indikator;
use App\Models\MonitoringIKU_Detail;


class MonitoringIKU extends Model
{
    use HasFactory;

    protected $table = 'monitoring_iku';
    protected $primaryKey = 'mti_id';
    public $incrementing = false;
    protected $fillable = [
        'mti_id',
        'th_id',
        'prodi_id',
        'status',
    ];

    public function targetIndikator()
    {
        return $this->belongsTo(target_indikator::class, 'prodi_id', 'prodi_id');
    }

    // public function prodi()
    // {
    //     return $this->belongsToThrough(program_studi::class, target_indikator::class);
    // }

    // public function tahunKerja()
    // {
    //     return $this->belongsToThrough(tahun_kerja::class, target_indikator::class);
    // }

    public function prodi()
    {
        return $this->belongsTo(program_studi::class, 'prodi_id', 'prodi_id');
    }

    // Relasi ke Tahun Kerja
    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'ik_id', 'ik_id');
    }

    public function monitoringikuDetail()
    {
        return $this->hasMany(MonitoringIKU_Detail::class, 'mti_id', 'mti_id');
    }

    public function isFilled()
    {
        $total = $this->monitoringikuDetail()->count();
        $filled = $this->monitoringikuDetail()
            ->whereNotNull('mtid_capaian')
            ->count();

        return $total > 0 && $filled === $total;
    }

    public function monitorings()
    {
        return $this->hasManyThrough(Monitoring::class, MonitoringIKU_Detail::class, 'mti_id', 'rk_id', 'mti_id', 'rk_id');
    }

    public function isCompleteForCurrentUnit()
    {
        $user = Auth::user();

        // 1. Jika Admin/Fakultas, anggap selalu false (atau true tergantung kebutuhan, tapi biasanya admin tidak mengisi)
        if ($user->role === 'admin' || $user->role === 'fakultas') {
            return false;
        }

        // 2. Ambil Semua ID Target Indikator (TI_ID) yang menjadi tanggung jawab Unit Kerja User ini
        //    untuk Prodi dan Tahun Monitoring ini.
        $targetIds = target_indikator::where('prodi_id', $this->prodi_id)
            ->where('th_id', $this->th_id)
            ->whereHas('indikatorKinerja', function ($query) use ($user) {
                // Filter berdasarkan unit_id milik user yang login
                $query->where('unit_id', $user->unit_id);
            })
            // Filter tambahan: Pastikan indikatornya IKU/IKT (opsional, sesuaikan kebutuhan)
            // ->whereHas('indikatorKinerja', function ($q) {
            //     $q->where('ik_jenis', 'IKU/IKT');
            // })
            ->pluck('ti_id');

        // 3. Jika unit ini tidak punya indikator sama sekali di prodi ini
        if ($targetIds->isEmpty()) {
            return false; // Tidak ada beban kerja, jadi tidak tombol finalisasi tidak perlu muncul (atau bisa return true jika dianggap selesai)
        }

        // 4. Ambil Data Detail yang SUDAH TERISI (Capaian tidak null & tidak kosong)
        //    Hanya ambil data milik targetIds di atas.
        $filledCount = MonitoringIKU_Detail::where('mti_id', $this->mti_id)
            ->whereIn('ti_id', $targetIds)
            ->whereNotNull('mtid_capaian')
            ->where('mtid_capaian', '!=', '') // Pastikan tidak string kosong
            ->count();

        // 5. Bandingkan Jumlah Target vs Jumlah yang Sudah Diisi
        //    Jika jumlah target sama dengan jumlah yang terisi, berarti LENGKAP.
        return $filledCount === $targetIds->count();
    }


    public $timestamps = true;

    public function isFinalForUnit($unitId)
    {
        return \App\Models\MonitoringFinalUnit::where('monitoring_iku_id', $this->mti_id)
            ->where('unit_id', $unitId)
            ->where('status', true)
            ->exists();
    }

}
