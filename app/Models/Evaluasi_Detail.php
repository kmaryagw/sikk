<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluasi_Detail extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_detail';

    protected $primaryKey = 'evald_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'evald_id',
        'eval_id',
        'ti_id',
        'evald_target',
        'evald_capaian',
        'evald_keterangan',
    ];

    public function evaluasi()
    {
        return $this->belongsTo(Evaluasi::class, 'eval_id', 'eval_id');
    }

    public function targetIndikator()
    {
        return $this->belongsTo(target_indikator::class, 'ti_id', 'ti_id');
    }

    public function unitKerja()
{
    return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id'); // Sesuaikan kolomnya
}

// public function rencanaKerja()
// {
//     return $this->hasManyThrough(
//         RencanaKerja::class,
//         RencanaKerjaTargetIndikator::class,
//         'ti_id', // Foreign key di RencanaKerjaTargetIndikator
//         'rk_id', // Foreign key di RencanaKerja
//         'ti_id', // Local key di Evaluasi (melalui target_indikator)
//         'rk_id'  // Local key di RencanaKerjaTargetIndikator
//     );
// }

}
