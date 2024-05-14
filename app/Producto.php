<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo', 'nombre', "abrev", 'descripcion', 'precio', 'tipo_venta',
        'stock_minimo', 'stock_actual', 'stock_actual_cantidad', 'estado', 'foto',
        'fecha_registro', 'status',
    ];

    protected $appends = ["saldo_producto"];

    public function ingresos()
    {
        return $this->belongsTo(IngresoProducto::class, 'producto_id');
    }

    public function kardex()
    {
        return $this->hasMany(KardexProducto::class, 'producto_id');
    }

    public function venta_detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'producto_id');
    }

    public function getSaldoProductoAttribute()
    {
        return number_format((float)$this->precio * (float)$this->stock_actual, 2, '.', '');
    }
}
