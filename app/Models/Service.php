<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'service';
    protected $fillable = ['nm_service', 'harga','jenis', 'pembagian', 'void'];

    public function cabang()
    {
        return $this->belongsToMany(Cabang::class, 'service_cabang', 'service_id', 'cabang_id')->withTimestamps();
    }

    public function serviceCabang()
    {
        return $this->hasMany(ServiceCabang::class, 'service_id', 'id');
    }
}
