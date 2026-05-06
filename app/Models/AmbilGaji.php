<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbilGaji extends Model
{
    use HasFactory;

    protected $table = 'ambil_gaji';
    protected $fillable = ['cabang_id', 'karyawan_id', 'jumlah', 'tgl', 'user_id', 'void'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
