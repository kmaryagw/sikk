<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class indikator_kinerja extends Model
{
    use HasFactory;

    protected $table = 'indikator_kinerja';

    // Primary key
    protected $primaryKey = 'ik_id';

    // Kolom yang dapat diisi
    protected $fillable = [
        'ik_id',
        'ik_nama',
        'std_id',
    ];

    public $timestamps = true;

    public function standar()
    {
        return $this->belongsTo(standar::class, 'std_id', 'std_id');
    }
}
