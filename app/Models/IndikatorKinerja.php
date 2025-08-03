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
    ];
    public function standar()
    {
        return $this->belongsTo(standar::class, 'std_id', 'std_id');
    }

    public function baselineTahun()
    {
        return $this->hasMany(IkBaselineTahun::class, 'ik_id', 'ik_id');
    }

    public $timestamps = true;
}
