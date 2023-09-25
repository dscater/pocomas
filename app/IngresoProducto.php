<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class IngresoProducto extends Model
{
    protected $fillable = [
        'nro_lote', 'proveedor_id', 'tipo', 'total_kilos', 'total_cantidad', 'precio_total', 'descripcion', 'saldo', 'precio_compra',
        'fecha_ingreso', 'fecha_registro', 'estado'
    ];

    protected $appends = ["cantidad_anterior"];
    public function getCantidadAnteriorAttribute()
    {
        if ($this->tipo_control == 'KILOS') {
            return $this->kilos;
        }
        return $this->cantidad;
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
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
            ->where("producto_id", $producto)
            ->where("ingreso_producto_id", $lote)
            ->where("stock_kilos", ">", 0)
            ->where("stock_cantidad", ">", 0)
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
}
