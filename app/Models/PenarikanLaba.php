<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenarikanLaba extends Model
{
    use HasFactory;

    protected $table = 'penarikan_laba';

    protected $fillable = [
        'investor_id',
        'tgl',
        'jumlah',
        'jenis',
        'pembayaran_id',
        'cabang_id'
    ];

    // protected $casts = [
    //     'tgl' => 'date',
    //     'jumlah' => 'double',
    //     'jenis' => 'int',
    //     'pembayaran_id' => 'int'
    // ];

    /**
     * Relasi ke model Investor (Belongs To)
     */
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }
}
