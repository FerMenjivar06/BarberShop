<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'fecha',
        'hora',
        'estado',
        'cliente_id',
        'barbero_id',
        'servicio_id'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function barbero()
    {
        return $this->belongsTo(Barbero::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
}