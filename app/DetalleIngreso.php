<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
    protected $fillable = [
        'ingreso_producto_id', 'producto_id', 'kilos', 'cantidad', 'tipo_control', 'stock_kilos', 'stock_cantidad', 'precio_compra', 'anticipo'

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
