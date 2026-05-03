<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;
    protected $table = 'cabang';
    protected $fillable = ['nama', 'possition', 'time_zome', 'off'];

    public function user()
    {
        return $this->hasMany(User::class, 'cabang_id', 'id');
    }
}
