<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKerjaTargetIndikator extends Model
{
    use HasFactory;

    protected $table = 'rencana_kerja_target_indikator';
    protected $primaryKey = 'rkti_id';
    
    protected $fillable = [
        'rkti_id',
        'rk_id',
        'ti_id',
    ];

    public function rencanaKerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }
    public function targetIndikator()  // Perbaikan di nama model, pastikan TargetIndikator ada
    {
        return $this->belongsTo(target_indikator::class, 'ti_id', 'ti_id');
    }
    

    public $timestamps = true;
}
