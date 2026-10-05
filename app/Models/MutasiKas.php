<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiKas extends Model
{
    use HasFactory;
    protected $table = 'mutasi_kas';
    protected $fillable = [
        'cabang_id', 
        'tgl', 
        'sumber_pembayaran_id', 
        'tujuan_pembayaran_id', 
        'jumlah', 
        'ket', 
        'void', 
        'user_id'
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
