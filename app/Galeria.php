<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Galeria extends Model
{
    protected $fillable = [
        'nombre', 'tipo', 'descripcion',
        'fecha_registro',
    ];

    public function imagenes()
    {
        return $this->hasMany(GaleriaImagen::class, 'galeria_id');
    }
}
