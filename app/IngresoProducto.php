<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class IngresoProducto extends Model
{
    protected $fillable = [
        'nro_lote', 'proveedor_id', 'producto_id', 'tipo',
        'total_kilos', 'total_cantidad', 'saldo_kilos', 'saldo_cantidad',
        'precio_total', 'descripcion', 'saldo', 'precio_compra',
        'fecha_ingreso', 'fecha_registro', 'estado'
    ];

    protected $appends = ["cantidad_anterior", "producto_principal", "existe_ventas", "existe_pagos"];
    public function getCantidadAnteriorAttribute()
    {
        if ($this->tipo_control == 'KILOS') {
            return $this->kilos;
        }
        return $this->cantidad;
    }

    public function getProductoPrincipalAttribute()
    {
        $producto_principal = DetalleIngreso::where("ingreso_producto_id", $this->id)
            ->where("producto_id", $this->producto_id)
            ->get()->first();

        return $producto_principal;
    }

    public function getExisteVentasAttribute()
    {
        $detalle_ingresos = DetalleIngreso::where("ingreso_producto_id", $this->id)->get();
        $existe_ventas = false;
        foreach ($detalle_ingresos as $di) {
            $venta_detalles = VentaDetalle::select("venta_detalles.*")
                ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                ->whereIn("ventas.estado", [1, 2])
                ->where("detalle_ingreso_id", "LIKE", "%$di->id%")
                ->get();
            foreach ($venta_detalles as $vd) {
                $ids = explode(",", $vd->detalle_ingreso_id);
                if (in_array($di->id, $ids)) {
                    $existe_ventas = true;
                    break 2;
                }
            }
        }
        return $existe_ventas;
    }

    public function getExistePagosAttribute()
    {
        $cuenta_pagar_detalles = CuentaPagarDetalle::select("cuenta_pagar_detalles.id")
            ->join("cuenta_pagars", "cuenta_pagars.id", "=", "cuenta_pagar_detalles.cuenta_pagar_id")
            ->where("cuenta_pagars.ingreso_producto_id", $this->id)
            ->get();
        if (count($cuenta_pagar_detalles) > 0) {
            return true;
        }
        return false;
    }

    // relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function detalle_ingresos()
    {
        return $this->hasMany(DetalleIngreso::class, 'ingreso_producto_id');
    }

    public function cuenta_pagars()
    {
        return $this->hasOne(CuentaPagar::class, 'ingreso_producto_id');
    }

    // ARMA STOCKS POR LOTE
    public static function getProductosLote($producto, $lote, $cantidad_kilos, $cantidad)
    {
        // OBTENER LOS LOTES CON STOCKS DISPONIBLES DE FORMA ASCENDENTE
        $detalle_ingresos = DetalleIngreso::select("detalle_ingresos.*")
            ->join("ingreso_productos", "ingreso_productos.id", "=", "detalle_ingresos.ingreso_producto_id")
            ->where("detalle_ingresos.producto_id", $producto)
            ->where("ingreso_producto_id", $lote)
            ->where("detalle_ingresos.stock_kilos", ">", 0)
            ->where("detalle_ingresos.stock_cantidad", ">", 0)
            ->where("ingreso_productos.estado", 1)
            ->orderBy("fecha_ingreso", "asc")->get();

        $array_lotes = [
            "ids" => [],
            "kilos" => [],
            "cantidad" => [],
        ];
        foreach ($detalle_ingresos as $di) {
            $total_disponible_kilos = (float)$di->stock_kilos - (float)$di->anticipo_kilos;
            $total_disponible_cantidad = (float)$di->stock_cantidad - (float)$di->anticipo;
            if ($cantidad_kilos <= $total_disponible_kilos && $cantidad <= $total_disponible_cantidad) {
                $array_lotes["ids"][] = $di->id;
                $array_lotes["cantidad"][] = (float)$cantidad;
                $array_lotes["kilos"][] = (float)$cantidad_kilos;
                break;
            } else {
                $array_lotes["ids"][] = $di->id;
                $array_lotes["kilos"][] = $total_disponible_kilos;
                $array_lotes["cantidad"][] = $total_disponible_cantidad;
                $cantidad_kilos = (float)$cantidad_kilos - $total_disponible_kilos;
                $cantidad = (float)$cantidad - $total_disponible_cantidad;
            }
        }

        return $array_lotes;
    }

    public static function actualizaSaldoStocks($ingreso_producto_id)
    {
        $suma_kilos = DetalleIngreso::where("ingreso_producto_id", $ingreso_producto_id)->sum("stock_kilos");
        $suma_cantidad = DetalleIngreso::where("ingreso_producto_id", $ingreso_producto_id)->sum("stock_cantidad");

        $ingreso_producto = IngresoProducto::find($ingreso_producto_id);
        $ingreso_producto->saldo_kilos = $suma_kilos;
        $ingreso_producto->saldo_cantidad = $suma_cantidad;
        $ingreso_producto->save();
        return $ingreso_producto;
    }
}
