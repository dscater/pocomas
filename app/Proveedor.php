<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
        'propietario', 'razon_social', 'fono', 'dir', 'fecha_registro',
    ];
}
