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
    ];

    protected $casts = [
        'tgl' => 'date',
        'jumlah' => 'double',
    ];

    /**
     * Relasi ke model Investor (Belongs To)
     */
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }
}
