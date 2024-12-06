<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKerjaTargetIndikator extends Model
{
    use HasFactory;

    protected $table = 'rencana_kerja_target_indikator';
    protected $primaryKey = 'rkti_id';
    public $incrementing = false;
    protected $fillable = [
        'rkti_id',
        'rk_id',
        'ti_id',
    ];

    public $timestamps = true;
}
