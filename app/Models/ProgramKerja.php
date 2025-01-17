<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramKerja extends Model
{
    use HasFactory;

    protected $table = 'rencana_kerja';
    protected $primaryKey = 'rk_id';
    protected $keyType = 'string';
    public $incrementing = false;
   protected $fillable = [
        'rk_id',
        'rk_nama',
        'th_id',
        'unit_id',
    ];
    public function tahun()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }
    public function unit()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }

    public function programStudis()
{
    return $this->belongsToMany(program_studi::class, 'rencana_kerja_program_studi', 'rk_id', 'prodi_id');
}

    public $timestamps = true;
}
