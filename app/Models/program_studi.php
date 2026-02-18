<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class program_studi extends Model
{
    use HasFactory;
    protected $table = 'program_studi';
    protected $primaryKey = 'prodi_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prodi_id',
        'nama_prodi',
        'id_fakultas',
        'singkatan_prodi',
    ];

    public function Fakultasn()
    {
        return $this->belongsTo(Fakultasn::class, 'id_fakultas', 'id_fakultas');
    }

    public function targetIndikator()
    {
        return $this->hasMany(target_indikator::class, 'prodi_id', 'prodi_id');
    }

    public function rencanaKerjas()
    {
        return $this->belongsToMany(RencanaKerja::class, 'rencana_kerja_program_studi', 'prodi_id', 'rk_id');
    }

    public function unitPengelola()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id_pengelola', 'unit_id');
    }
    }
