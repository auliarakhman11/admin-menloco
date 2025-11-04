<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;
    protected $table = 'jurnal';
    protected $fillable = ['cabang_id', 'akun_id', 'jumlah', 'ket', 'jenis', 'tgl', 'void', 'user_id'];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
