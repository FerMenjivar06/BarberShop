<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = "servicios"; 
    
    protected $fillable = ['nombre', 'descripcion', 'precio'];

    protected $casts = [
        'precio' => 'decimal:2'
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}