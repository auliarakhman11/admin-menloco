<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCabang extends Model
{
    use HasFactory;

    protected $table = 'service_cabang';
    protected $fillable = ['service_id', 'cabang_id'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }
}
