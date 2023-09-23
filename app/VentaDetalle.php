<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $fillable = [
        'venta_id', 'producto_id', "detalle_ingreso_id", "lotes_cantidades", 'cantidad', 'monto', 'descuento', 'sub_total',
    ];

    protected $appends = ["total_sd"];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function cuenta_cobrar_detalle()
    {
        return $this->hasOne(CuentaCobrarDetalle::class, 'venta_detalle_id');
    }

    public function getTotalSdAttribute()
    {
        return number_format((float)$this->sub_total + $this->descuento, 2, '.', '');
    }
}
