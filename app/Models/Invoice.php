<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoice';
    protected $fillable = ['cabang_id', 'no_invoice', 'nm_customer', 'no_tlp', 'total', 'diskon', 'tgl', 'no_antrian', 'selesai', 'void', 'user_id', 'user_refund', 'ket_refund'];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'invoice_id', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }

    public function penjualanKaryawan()
    {
        return $this->hasMany(PenjualanKaryawan::class, 'invoice_id', 'id');
    }
}
