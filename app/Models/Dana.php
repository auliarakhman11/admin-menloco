<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dana extends Model
{
    use HasFactory;
    protected $table = 'dana';
    protected $fillable = ['tgl', 'jenis', 'jumlah', 'ket', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
