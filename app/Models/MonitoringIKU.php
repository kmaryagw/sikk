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

        // Ambil semua target indikator milik unit kerja user
        $targetIds = target_indikator::where('prodi_id', $this->prodi_id)
            ->where('th_id', $this->th_id)
            ->whereHas('indikatorKinerja', function ($query) use ($user) {
                $query->where('unit_id', $user->unit_id);
            })
            ->pluck('ti_id');

        if ($targetIds->isEmpty()) {
            \Log::info('FINAL CHECK: Tidak ada indikator untuk unit kerja ini', [
                'mti_id' => $this->mti_id,
                'user_unit_id' => $user->unit_id,
            ]);
            return false; // unit ini tidak punya indikator yang perlu diisi
        }

        // Ambil semua detail monitoring yang sesuai dengan target indikator tersebut
        $details = MonitoringIKU_Detail::where('mti_id', $this->mti_id)
            ->whereIn('ti_id', $targetIds)
            ->get(['ti_id', 'mtid_capaian']);

        // Log isi data mentah untuk debugging
        // \Log::info('FINAL CHECK DATA', [
        //     'mti_id' => $this->mti_id,
        //     'targetIds' => $targetIds,
        //     'filledDetails' => $details->pluck('mtid_capaian', 'ti_id'),
        // ]);

        // Cek apakah semua indikator unit kerja sudah memiliki capaian
        foreach ($targetIds as $ti_id) {
            $detail = $details->firstWhere('ti_id', $ti_id);

            if (!$detail || $detail->mtid_capaian === null || $detail->mtid_capaian === '') {
                \Log::info('FINAL CHECK RESULT', [
                    'mti_id' => $this->mti_id,
                    'status' => 'Belum lengkap',
                    'missing_ti_id' => $ti_id,
                ]);
                return false; // masih ada yang belum diisi
            }
        }

        // \Log::info('FINAL CHECK RESULT', [
        //     'mti_id' => $this->mti_id,
        //     'status' => 'Lengkap',
        // ]);

        return true; // semua indikator unit kerja sudah diisi
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
