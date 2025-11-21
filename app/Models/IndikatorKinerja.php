<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerja extends Model
{
    use HasFactory;

    protected $table = 'indikator_kinerja';
    protected $primaryKey = 'ik_id';
    public $incrementing = false;
   protected $fillable = [
        'ik_id',
        'ik_nama',
        'ik_jenis',
        'ik_kode',
        'ik_ketercapaian',
        'ik_baseline',
        'ik_is_aktif',
        'std_id',
        // 'unit_id', // tambahkan ini
    ];
    public function standar()
    {
        return $this->belongsTo(standar::class, 'std_id', 'std_id');
    }

    public function baselineTahun()
    {
        return $this->hasMany(IkBaselineTahun::class, 'ik_id', 'ik_id');
    }

    // public function unitKerja()
    // {
    //     return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    // }

    public function unitKerja()
    {
        return $this->belongsToMany(UnitKerja::class, 'indikatorkinerja_unitkerja', 'ik_id', 'unit_id')
                    ->withPivot('ik_id', 'unit_id'); // Menyertakan kolom pivot jika diperlukan
    }

    public function targetIndikator()
    {
        return $this->hasMany(target_indikator::class, 'ik_id', 'ik_id');
    }

    public $timestamps = true;
}
