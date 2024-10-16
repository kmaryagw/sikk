<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKerja extends Model
{
    use HasFactory;
    protected $table = 'rencana_kerja';
    protected $primaryKey = 'rk_id';
    public $incrementing = false;
    protected $fillable = [
        'rk_id',
        'rk_nama',
        'th_id',
        'unit_id',
    ];

    public function tahun_kerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function UnitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }

    public $timestamps = true;
}
