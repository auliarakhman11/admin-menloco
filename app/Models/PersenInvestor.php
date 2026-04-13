<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersenInvestor extends Model
{
    use HasFactory;

    protected $table = 'persen_investor';
    protected $fillable = ['investor_id', 'cabang_id', 'persen'];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id', 'id');
    }
}
