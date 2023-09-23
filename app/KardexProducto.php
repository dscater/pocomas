<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Producto;
use App\IngresoProducto;

class KardexProducto extends Model
{
    protected $fillable = [
        'producto_id', 'detalle_ingreso_id', 'fecha', 'detalle', 'precio',
        'tipo', 'ingreso_c', 'salida_c',
        'saldo_c', 'cu', 'ingreso_m',
        'salida_m', 'saldo_m',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // REGISTRAR INGRESO
    public static function registroIngreso(Producto $producto, IngresoProducto $ingreso, DetalleIngreso $detalle_ingreso, $mensaje = null)
    {
        //buscar el ultimo registro y usar sus valores
        $ultimo = KardexProducto::where('producto_id', $producto->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->last();
        $cantidad = 0;
        if ($detalle_ingreso->producto->medida == 'KILOS') {
            $cantidad = $detalle_ingreso->kilos;
        } else {
            $cantidad = $detalle_ingreso->cantidad;
        }
        if ($ultimo) {
            KardexProducto::create([
                'producto_id' => $producto->id,
                'detalle_ingreso_id' => $detalle_ingreso->id,
                'fecha' => date('Y-m-d'),
                'detalle' => $mensaje ? $mensaje : 'COMPRA DE PRODUCTO - LOTE NRO. ' . $ingreso->nro_lote,
                'precio' => $detalle_ingreso->precio_compra,
                'tipo' => 'INGRESO',
                'ingreso_c' => $cantidad,
                'saldo_c' => (float)$ultimo->saldo_c + (float)$cantidad,
                'cu' => $producto->precio,
                'ingreso_m' => (float)$cantidad * (float)$producto->precio,
                'saldo_m' => (float)$ultimo->saldo_m + ((float)$cantidad * (float)$producto->precio)
            ]);
        } else {
            KardexProducto::create([
                'producto_id' => $producto->id,
                'detalle_ingreso_id' => $detalle_ingreso->id,
                'fecha' => date('Y-m-d'),
                'detalle' => 'VALOR INICIAL',
                'precio' => $detalle_ingreso->precio_compra,
                'tipo' => 'INGRESO',
                'ingreso_c' => $cantidad,
                'saldo_c' => (float)$cantidad,
                'cu' => $producto->precio,
                'ingreso_m' => (float)$cantidad * (float)$producto->precio,
                'saldo_m' => (float)$cantidad * (float)$producto->precio
            ]);
        }
        return true;
    }



    // REGISTRAR SOLO INGRESOS
    public static function registroSoloIngreso(Producto $producto, $cantidad, $detalle_ingreso, $mensaje = null)
    {
        //buscar el ultimo registro y usar sus valores
        $ultimo = KardexProducto::where('producto_id', $producto->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->last();
        $cantidad = $cantidad;
        KardexProducto::create([
            'producto_id' => $producto->id,
            'detalle_ingreso_id' => $detalle_ingreso,
            'fecha' => date('Y-m-d'),
            'detalle' => $mensaje ? $mensaje : 'INGRESO DE PRODUCTO',
            'tipo' => 'INGRESO',
            'ingreso_c' => $cantidad,
            'saldo_c' => (float)$ultimo->saldo_c + (float)$cantidad,
            'cu' => $producto->precio,
            'ingreso_m' => (float)$cantidad * (float)$producto->precio,
            'saldo_m' => (float)$ultimo->saldo_m + ((float)$cantidad * (float)$producto->precio)
        ]);
        return true;
    }

    // REGISTRAR EGRESO
    public static function registroEgreso(Producto $producto, $cantidad, $detalle_ingreso, $mensaje = null)
    {
        //buscar el ultimo registro y usar sus valores
        $ultimo = KardexProducto::where('producto_id', $producto->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->last();
        $cantidad = $cantidad;
        KardexProducto::create([
            'producto_id' => $producto->id,
            'detalle_ingreso_id' => $detalle_ingreso,
            'fecha' => date('Y-m-d'),
            'detalle' => $mensaje ? $mensaje : 'VENTA DE PRODUCTO',
            'tipo' => 'EGRESO',
            'salida_c' => $cantidad,
            'saldo_c' => (float)$ultimo->saldo_c - (float)$cantidad,
            'cu' => $producto->precio,
            'salida_m' => (float)$cantidad * (float)$producto->precio,
            'saldo_m' => (float)$ultimo->saldo_m - ((float)$cantidad * (float)$producto->precio)
        ]);
        return true;
    }
}
