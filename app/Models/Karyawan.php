<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawan';
    protected $fillable = ['cabang_id', 'nama', 'no_tlp', 'alamat', 'tgl_masuk', 'void', 'gapok', 'persen_gaji', 'bank', 'no_rek', 'pembagian'];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }
}
