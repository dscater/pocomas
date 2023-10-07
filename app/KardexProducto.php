<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Producto;
use App\IngresoProducto;

class KardexProducto extends Model
{
    protected $fillable = [
        'producto_id', 'detalle_ingreso_id', 'modulo', 'fecha', 'detalle', 'precio',
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
        $cantidad = $detalle_ingreso->kilos;

        if ($ultimo) {
            KardexProducto::create([
                'producto_id' => $producto->id,
                'detalle_ingreso_id' => $detalle_ingreso->id,
                'modulo' => "DetalleIngreso",
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

    // REGISTRAR INGRESO LOTE
    public static function registroIngresoLote(Producto $producto, IngresoProducto $ingreso, $mensaje = null, $modulo = "IngresoProducto")
    {
        //buscar el ultimo registro y usar sus valores
        $ultimo = KardexProducto::where('producto_id', $producto->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->last();
        $cantidad = 0;
        $cantidad = $ingreso->total_kilos;

        if ($ultimo) {
            KardexProducto::create([
                'producto_id' => $producto->id,
                'detalle_ingreso_id' => $ingreso->id,
                'modulo' => $modulo,
                'fecha' => date('Y-m-d'),
                'detalle' => $mensaje ? $mensaje : 'COMPRA DE PRODUCTO - LOTE NRO. ' . $ingreso->nro_lote,
                'precio' => $ingreso->precio_compra,
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
                'detalle_ingreso_id' => $ingreso->id,
                "modulo" => $modulo,
                'fecha' => date('Y-m-d'),
                'detalle' => 'VALOR INICIAL LOTE N° ' . $ingreso->nro_lote,
                'precio' => $ingreso->precio_compra,
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
    public static function registroSoloIngreso(Producto $producto, $cantidad, $detalle_ingreso, $mensaje = null, $modulo = "DetalleIngreso")
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
            'modulo' => $modulo,
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
    public static function registroEgreso(Producto $producto, $cantidad, $detalle_ingreso, $mensaje = null, $modulo = "DetalleIngreso")
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
            "modulo" => $modulo,
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
