<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class target_indikator extends Model
{
    use HasFactory;

    protected $table = 'target_indikator';

    // Menentukan primary key
    protected $primaryKey = 'ti_id';

    protected $keyType = 'string';

    protected $fillable = [
        'ti_id',
        'ik_id',
        'ti_target',
        'ti_keterangan',
        'prodi_id',
        'th_id'
    ];

    public $timestamps = true;

    public function indikatorKinerja()
    {
        return $this->belongsTo(indikator_kinerja::class, 'ik_id', 'ik_id');
    }

    public function prodi()
    {
        return $this->belongsTo(program_studi::class, 'prodi_id','prodi_id');
    }

    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

}
