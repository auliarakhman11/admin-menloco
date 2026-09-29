<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoOperasional extends Model
{
    use HasFactory;

    protected $table = 'saldo_oprasional';

    protected $fillable = [
        'tgl',
        'akun_id',
        'cabang_id',
        'pembayaran_id',
        'jenis',
        'jumlah',
        'ket',
        'user_id',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }
}
