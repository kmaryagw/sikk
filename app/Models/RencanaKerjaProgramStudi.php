<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKerjaProgramStudi extends Model
{
    use HasFactory;

    protected $table = 'rencana_kerja_program_studi';
    protected $primaryKey = 'rkps_id';
    public $incrementing = false;
    protected $fillable = [
        'rk_id',
        'prodi_id',
    ];

    public $timestamps = true;
}
