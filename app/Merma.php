<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merma extends Model
{
    protected $fillable = [
        "ingreso_producto_id",
        "producto_id",
        "fecha",
        "cantidad_kilos",
        "cantidad",
        "porcentaje",
    ];

    public function ingreso_producto()
    {
        return $this->belongsTo(IngresoProducto::class, 'ingreso_producto_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
