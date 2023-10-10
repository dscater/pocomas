<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merma extends Model
{
    protected $fillable = [
        "detalle_ingreso_id",
        "fecha",
        "cantidad_kilos",
        "cantidad",
        "porcentaje",
    ];

    public function detalle_ingreso()
    {
        return $this->belongsTo(DetalleIngreso::class, 'detalle_ingreso_id');
    }
}
