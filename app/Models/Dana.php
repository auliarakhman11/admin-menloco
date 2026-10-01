<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dana extends Model
{
    use HasFactory;
    protected $table = 'dana';
    protected $fillable = ['cabang_id', 'tgl', 'jenis', 'jenis_dana', 'jenis_saldo', 'pembayaran_id', 'jumlah', 'ket', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
