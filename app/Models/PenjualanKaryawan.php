<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanKaryawan extends Model
{
    use HasFactory;
    protected $table = 'penjualan_karyawan';
    protected $fillable = ['invoice_id', 'cabang_id', 'karyawan_id', 'harga', 'tgl', 'user_id', 'void'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
