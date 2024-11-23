<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    protected $table = 'unit_kerja';
    protected $primaryKey = 'unit_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unit_id',
        'unit_nama',
        'unit_kerja',
    ];

    public function evaluasiDetails()
    {
        return $this->hasMany(Evaluasi_Detail::class, 'unit_id', 'unit_id');
    }
}
