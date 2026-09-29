<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;
    protected $table = 'akun';
    protected $fillable = ['nm_akun', 'jml_pengeluaran'];

    public function jurnal()
    {
        return $this->hasMany(Jurnal::class, 'akun_id', 'id');
    }

    public function pengeluaranAkun()
    {
        return $this->hasMany(PengeluaranAkun::class, 'akun_id', 'id');
    }

    public function saldoOperasional()
    {
        return $this->hasMany(SaldoOperasional::class, 'akun_id', 'id');
    }
}
