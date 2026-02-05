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
        if ($total === 0) return false;

        $filled = $this->monitoringikuDetail()
            ->whereNotNull('mtid_capaian')
            ->where('mtid_capaian', '!=', '')
            ->count();

        return $filled === $total;
    }

    public function monitorings()
    {
        return $this->hasManyThrough(Monitoring::class, MonitoringIKU_Detail::class, 'mti_id', 'rk_id', 'mti_id', 'rk_id');
    }

    public function isCompleteForCurrentUnit()
    {
        $user = Auth::user();

        // Admin dan Fakultas biasanya hanya memantau, tidak mengisi
        if ($user->role === 'admin' || $user->role === 'fakultas') {
            return false;
        }

        // Cari semua target indikator yang menjadi tanggung jawab unit/prodi ini
        $query = target_indikator::where('prodi_id', $this->prodi_id)
            ->where('th_id', $this->th_id);

        if ($user->role === 'unit kerja') {
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) use ($user) {
                $q->where('unit_kerja.unit_id', $user->unit_id);
            });
        } elseif ($user->role === 'prodi') {
            // Jika user prodi, dia bertanggung jawab atas prodinya sendiri
            $query->where('prodi_id', $user->prodi_id);
        }

        $targetIds = $query->pluck('ti_id');

        if ($targetIds->isEmpty()) {
            return false;
        }

        // Hitung yang sudah diisi oleh unit/prodi tersebut
        $filledCount = MonitoringIKU_Detail::where('mti_id', $this->mti_id)
            ->whereIn('ti_id', $targetIds)
            ->whereNotNull('mtid_capaian')
            ->where('mtid_capaian', '!=', '')
            ->count();

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
