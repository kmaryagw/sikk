<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'username',
        'status',
        'password',
        'role',
        'prodi_id',
        'unit_id',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke tabel program_kerja
     */
    public function programStudi()
    {
        return $this->belongsTo(program_studi::class, 'prodi_id', 'prodi_id');
    }

    /**
     * Relasi ke tabel unit_kerja
     */
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }
}