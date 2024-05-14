<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaLote extends Model
{
    protected $fillable = [
        "ingreso_producto_id",
        "detalle_ingreso_id",
        "venta_detalle_id",
        "producto_id",
        "cantidad_kilos",
        "cantidad",
        "precio",
        "fecha"
    ];

    public function ingreso_producto()
    {
        return $this->belongsTo(IngresoProducto::class, 'ingreso_producto_id');
    }

    public function venta_detalle()
    {
        return $this->belongsTo(VentaDetalle::class, 'venta_detalle_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
