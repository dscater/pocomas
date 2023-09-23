<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GaleriaImagen extends Model
{
    protected $fillable = [
        'galeria_id', 'imagen', 'fecha_registro'
    ];

    public function galeria()
    {
        return $this->belongsTo(Galeria::class, 'galeria_id');
    }
}
