<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;
    protected $table = 'cabang';
    protected $fillable = ['nama', 'possition', 'time_zone', 'off'];

    public function user()
    {
        return $this->hasMany(User::class, 'cabang_id', 'id');
    }

    public function pengeluaranAkun()
    {
        return $this->hasMany(PengeluaranAkun::class, 'cabang_id', 'id');
    }
}
