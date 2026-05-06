<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualan';
    protected $fillable = ['invoice_id', 'cabang_id', 'service_id', 'harga', 'qty', 'tgl', 'void', 'user_id'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }
}
