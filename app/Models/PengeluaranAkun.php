<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranAkun extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_akun';
    protected $fillable = ['akun_id', 'cabang_id', 'jenis', 'jumlah'];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'aksun_id', 'id');
    }
}
