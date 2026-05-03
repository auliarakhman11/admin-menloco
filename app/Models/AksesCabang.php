<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AksesCabang extends Model
{
    use HasFactory;
    protected $table = 'akses_cabang';
    protected $fillable = ['cabang_id', 'user_id'];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }
}
