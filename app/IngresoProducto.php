<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    public static function getProductosLote($producto, $cantidad)
    {
        // OBTENER LOS LOTES CON STOCKS DISPONIBLES DE FORMA ASCENDENTE
        $detalle_ingresos = DetalleIngreso::select("detalle_ingresos.*")
            ->join("ingreso_productos", "ingreso_productos.id", "=", "detalle_ingresos.ingreso_producto_id")
            ->where("producto_id", $producto)
            ->where("stock_kilos", ">", 0)
            ->where("ingreso_productos.estado", 1)
            ->orderBy("fecha_ingreso", "asc")->get();

        $array_lotes = [
            "ids" => [],
            "cantidades" => []
        ];
        foreach ($detalle_ingresos as $di) {
            $total_disponible = (float)$di->stock_kilos - (float)$di->anticipo;
            if ($cantidad <= $total_disponible) {
                $array_lotes["ids"][] = $di->id;
                $array_lotes["cantidades"][] = (float)$cantidad;
                break;
            } else {
                $array_lotes["ids"][] = $di->id;
                $array_lotes["cantidades"][] = $total_disponible;
                $cantidad = (float)$cantidad - $total_disponible;
            }
        }

        return $array_lotes;
    }
}
