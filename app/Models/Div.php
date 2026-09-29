<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Div extends Model
{
    use HasFactory;
    protected $table = 'div';
    protected $fillable = ['nm_div', 'jml_pengeluaran'];
}
