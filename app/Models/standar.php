<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class standar extends Model
{
    use HasFactory;

    protected $table = 'standar';
    protected $primaryKey = 'std_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'std_id',
        'std_nama',
        'std_deskripsi',
        'std_url',
        'std_kategori',
    ];
}