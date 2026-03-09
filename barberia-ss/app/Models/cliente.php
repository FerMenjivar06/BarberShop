<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'apellido', 'telefono', 'correo'];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}