<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $table = 'investor';
    protected $fillable = ['nm_investor'];

    public function persenInvestor()
    {
        return $this->hasMany(PersenInvestor::class, 'investor_id', 'id');
    }
}
