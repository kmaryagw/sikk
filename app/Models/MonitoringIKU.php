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

        // 1. Admin/Fakultas tidak mengisi capaian, jadi return false agar tombol tidak muncul bagi mereka
        if ($user->role === 'admin' || $user->role === 'fakultas' || $user->role === 'prodi') {
            return false;
        }

        // 2. Ambil Target Indikator (TI_ID) yang menjadi tanggung jawab Unit/Prodi ini
        $query = target_indikator::where('prodi_id', $this->prodi_id)
            ->where('th_id', $this->th_id);

        // LOGIC FILTER BERDASARKAN TABEL PIVOT
        if ($user->role === 'unit kerja') {
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) use ($user) {
                // Kita cek ke relasi unitKerja (yang terhubung lewat tabel pivot)
                $q->where('unit_kerja.unit_id', $user->unit_id);
            });
        } elseif ($user->role === 'prodi') {
            // Jika prodi, biasanya bertanggung jawab atas semua indikator di prodinya sendiri
            // atau jika prodi juga difilter per unit, gunakan logic yang sama dengan unit kerja
            $query->where('prodi_id', $user->prodi_id);
        }

        $targetIds = $query->pluck('ti_id');

        // 3. Jika tidak ada beban kerja untuk unit ini di prodi ini, tombol tidak muncul
        if ($targetIds->isEmpty()) {
            return false;
        }

        // 4. Hitung detail yang SUDAH TERISI (Capaian tidak null & tidak kosong)
        // Hanya hitung baris detail yang ID-nya ada dalam daftar tanggung jawab unit ini
        $filledCount = MonitoringIKU_Detail::where('mti_id', $this->mti_id)
            ->whereIn('ti_id', $targetIds)
            ->whereNotNull('mtid_capaian')
            ->where('mtid_capaian', '!=', '')
            ->count();

        // 5. Bandingkan: Apakah jumlah yang diisi sudah sama dengan jumlah beban kerja?
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
