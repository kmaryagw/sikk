<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisasiJabatan extends Model
{
    use HasFactory;

    protected $table = 'organisasi_jabatan';
    protected $primaryKey = 'oj_id';
    public $incrementing = false;

    protected $fillable = [
        'oj_id',
        'oj_nama',
        'oj_mengeluarkan_nomor',
        'oj_kode',
        'oj_induk'
    ];

    public function parent()
    {
        return $this->belongsTo(OrganisasiJabatan::class, 'oj_induk', 'oj_id');
    }


    public function children()
    {
        return $this->hasMany(OrganisasiJabatan::class, 'oj_induk', 'oj_id');
    }
}