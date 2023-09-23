<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CajaCentral extends Model
{
    protected $fillable = [
        'fecha', 'monto', 'descripcion', 'concepto_id', 'ingreso_producto_id', 'cuenta_pagar_id', 'tipo', 'sw_egreso', 'tipo_transaccion', 'fecha_registro',
    ];

    public static function getSaldo()
    {
        $ingreso_total = CajaCentral::where('tipo', 'INGRESO')->sum("monto");
        $egreso_total = CajaCentral::where('tipo', 'EGRESO')->sum("monto");
        return (float)$ingreso_total - (float)$egreso_total;
    }

    public static function getSaldoCaja()
    {
        $ingreso_total = CajaCentral::where('tipo', 'INGRESO')->where("tipo_transaccion", "CAJA")->sum("monto");
        $egreso_total = CajaCentral::where('tipo', 'EGRESO')->where("tipo_transaccion", "CAJA")->sum("monto");
        return (float)$ingreso_total - (float)$egreso_total;
    }

    public static function getSaldoBanco()
    {
        $ingreso_total = CajaCentral::where('tipo', 'INGRESO')->where("tipo_transaccion", "BANCO")->sum("monto");
        $egreso_total = CajaCentral::where('tipo', 'EGRESO')->where("tipo_transaccion", "BANCO")->sum("monto");
        return (float)$ingreso_total - (float)$egreso_total;
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'concepto_id');
    }

    public function ingreso_producto()
    {
        return $this->belongsTo(IngresoProducto::class, 'ingreso_producto_id');
    }
}
