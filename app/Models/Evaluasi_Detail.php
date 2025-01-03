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
        'evald_status',
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
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'rk_id', 'rk_id');
    }

    public function rencanaKerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }
    
}
