<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuentaPagar extends Model
{
    protected $fillable = [
        'ingreso_producto_id', 'proveedor_id', 'monto_total', 'saldo', 'descripcion',
        'fecha_registro',
    ];

    public function ingreso_producto()
    {
        return $this->belongsTo(IngresoProducto::class, 'ingreso_producto_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function cuenta_pagar_detalles()
    {
        return $this->hasMany(CuentaPagarDetalle::class, 'cuenta_pagar_id');
    }
}
