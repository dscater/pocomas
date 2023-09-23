<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RazonSocial extends Model
{
    protected $fillable = [
        'nombre', 'alias', 'nombre_propietario', 'pais',
        'ciudad', 'dir', 'nit', 'nro_aut', 'fono',
        'cel', 'correo', 'logo', 'actividad_economica',
        'fecha_registro',
    ];
}
