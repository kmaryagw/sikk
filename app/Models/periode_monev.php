<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class periode_monev extends Model
{
    use HasFactory;

    protected $table = 'periode_monev';
    protected $primaryKey = 'pm_id';
    public $incrementing = false;
   protected $fillable = [
        'pm_id',
        'pm_nama',
    ];

    public $timestamps = true;

}
