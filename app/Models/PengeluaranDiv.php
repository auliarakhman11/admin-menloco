<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranDiv extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_div';
    protected $fillable = ['cabang_id', 'div_id', 'jenis', 'jumlah'];

    public function div()
    {
        return $this->belongsTo(Div::class, 'div_id', 'id');
    }
}
