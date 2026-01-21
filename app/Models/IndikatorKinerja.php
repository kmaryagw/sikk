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
    protected $keyType = 'string'; 

    protected $fillable = [
        'ik_id',
        'ik_nama',
        'ik_jenis',
        'ik_kode',
        'ik_ketercapaian',
        'ik_baseline',
        'ik_is_aktif',
        'std_id',
    ];

    public function standar()
    {
        return $this->belongsTo(Standar::class, 'std_id', 'std_id')->withDefault([
            'std_nama' => 'Tidak memiliki standar',
            'std_deskripsi' => 'Standar telah dihapus atau belum ditentukan'
        ]);
    }

    public function baselineTahun()
    {
        return $this->hasMany(IkBaselineTahun::class, 'ik_id', 'ik_id');
    }

    public function unitKerja()
    {
        return $this->belongsToMany(UnitKerja::class, 'indikatorkinerja_unitkerja', 'ik_id', 'unit_id')
                    ->withPivot('ik_id', 'unit_id'); 
    }

    public function targetIndikator()
    {
        return $this->hasMany(target_indikator::class, 'ik_id', 'ik_id');
    }

    public $timestamps = true;
}