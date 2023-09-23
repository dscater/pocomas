<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuentaCobrarDetalle extends Model
{
    protected $fillable = [
        "cuenta_cobrar_id",
        "venta_detalle_id",
        "monto",
        "cancelado",
        "valor",
        "saldo",
    ];

    public function cuenta_cobrar()
    {
        return $this->belongsTo(CuentaCobrar::class, 'cuenta_cobrar_id');
    }

    public function venta_detalle()
    {
        return $this->belongsTo(VentaDetalle::class, 'venta_detalle_id');
    }
}
