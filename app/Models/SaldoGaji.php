<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoGaji extends Model
{
    use HasFactory;

    protected $table = 'saldo_gaji';

    protected $fillable = [
        'tgl',
        'cabang_id',
        'pembayaran_id',
        'jenis',
        'jumlah',
        'ket',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }
}
