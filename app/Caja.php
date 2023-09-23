<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'fecha_registro', 'estado'
    ];

    protected $append = ["saldo_actual"];

    // ATTRIBUTOS
    public function getSaldoActualAttribute()
    {
        return self::getSaldo($this->id);
    }

    // RELACIONES
    public function user()
    {
        return $this->hasMany(UserCaja::class, 'caja_id');
    }

    public function inicio_cajas()
    {
        return $this->hasMany(InicioCaja::class, 'caja_id');
    }

    public function cierre_cajas()
    {
        return $this->hasMany(CierreCaja::class, 'caja_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'caja_id');
    }

    public function ingresos()
    {
        return $this->hasMany(IngresoCaja::class, 'caja_id');
    }

    public function cuenta_pagos()
    {
        return $this->hasMany(CuentaPago::class, 'caja_id');
    }

    // GET SALDO CAJA
    public static function getSaldo($id)
    {
        $caja = Caja::findOrFail($id);
        $ingresos = IngresoCaja::where("tipo_movimiento", "INGRESO")->where('caja_id', $caja->id)->sum('monto_total');
        $egresos = IngresoCaja::where("tipo_movimiento", "EGRESO")->where('caja_id', $caja->id)->sum('monto_total');
        return (float)$ingresos - (float)$egresos;
    }

    // GET SUMA BANCOS
    public static function getSumaBancos($id)
    {
        $caja = Caja::findOrFail($id);
        $ventas = IngresoCaja::select("ingreso_cajas.monto_total")
            ->join("ventas", "ventas.id", "=", "ingreso_cajas.registro_id")
            ->where("ingreso_cajas.estado", 1)
            ->where("ingreso_cajas.tipo_movimiento", "INGRESO")
            ->whereIn("ingreso_cajas.tipo", ["ANTICIPO VENTA", "CANCELACIÓN DE ANTICIPO", "VENTA"])
            ->where('ingreso_cajas.caja_id', $caja->id)
            ->where("ventas.tipo_venta", "BANCO")
            ->sum('ingreso_cajas.monto_total');

        $por_cobrar = IngresoCaja::select("ingreso_cajas.monto_total")
            ->join("cuenta_pagos", "cuenta_pagos.id", "=", "ingreso_cajas.registro_id")
            ->where("ingreso_cajas.estado", 1)
            ->where("ingreso_cajas.tipo_movimiento", "INGRESO")
            ->where("ingreso_cajas.tipo", "PAGO POR COBRAR")
            ->where('ingreso_cajas.caja_id', $caja->id)
            ->where("cuenta_pagos.tipo_cobro", "BANCO")
            ->sum('ingreso_cajas.monto_total');
        $suma_total = (float)$ventas + (float)$por_cobrar;
        return (float)$suma_total;
    }


    // GET SUMA OTROS
    public static function getSumaOtros($id)
    {
        $caja = Caja::findOrFail($id);
        $ventas = IngresoCaja::select("ingreso_cajas.monto_total")
            ->join("ventas", "ventas.id", "=", "ingreso_cajas.registro_id")
            ->where("ingreso_cajas.estado", 1)
            ->where("ingreso_cajas.tipo_movimiento", "INGRESO")
            ->whereIn("ingreso_cajas.tipo", ["ANTICIPO VENTA", "CANCELACIÓN DE ANTICIPO", "VENTA"])
            ->where('ingreso_cajas.caja_id', $caja->id)
            ->where("ventas.tipo_venta", "!=", "BANCO")
            ->sum('ingreso_cajas.monto_total');

        $por_cobrar = IngresoCaja::select("ingreso_cajas.monto_total")
            ->join("cuenta_pagos", "cuenta_pagos.id", "=", "ingreso_cajas.registro_id")
            ->where("ingreso_cajas.estado", 1)
            ->where("ingreso_cajas.tipo_movimiento", "INGRESO")
            ->where("ingreso_cajas.tipo", "PAGO POR COBRAR")
            ->where('ingreso_cajas.caja_id', $caja->id)
            ->where("cuenta_pagos.tipo_cobro", "!=", "BANCO")
            ->sum('ingreso_cajas.monto_total');
        $suma_total = (float)$ventas + (float)$por_cobrar;

        $egresos = IngresoCaja::select("ingreso_cajas.monto_total")
            ->where("ingreso_cajas.estado", 1)
            ->where("ingreso_cajas.tipo_movimiento", "EGRESO")
            ->where('ingreso_cajas.caja_id', $caja->id)
            ->sum('ingreso_cajas.monto_total');

        $ingresos = IngresoCaja::select("ingreso_cajas.monto_total")
            ->where("ingreso_cajas.estado", 1)
            ->where("ingreso_cajas.tipo_movimiento", "INGRESO")
            ->whereNotIn("ingreso_cajas.tipo", ["ANTICIPO VENTA", "CANCELACIÓN DE ANTICIPO", "VENTA", "PAGO POR COBRAR"])
            ->where('ingreso_cajas.caja_id', $caja->id)
            ->sum('ingreso_cajas.monto_total');

        $suma_total = (float)$suma_total - (float)$egresos + $ingresos;
        return (float)$suma_total;
    }


    // GET SALDO POR FECHA
    public static function getSaldoFecha($id, $fecha)
    {
        $caja = Caja::findOrFail($id);
        $ingresos = IngresoCaja::where("tipo_movimiento", "INGRESO")->where('caja_id', $caja->id)->where('fecha', $fecha)->sum('monto_total');
        $egresos = IngresoCaja::where("tipo_movimiento", "EGRESO")->where('caja_id', $caja->id)->where('fecha', $fecha)->sum('monto_total');
        return (float)$ingresos - (float)$egresos;
    }
}
