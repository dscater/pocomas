<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Venta extends Model
{
    protected $fillable = [
        'caja_id', 'user_id', 'cliente_id', 'cantidad_total_kilos', 'cantidad_total', "anticipo", "saldo",
        'monto_total', 'tipo_venta', 'fecha_venta', 'hora_venta', "monto_recibido", "monto_cambio",
        'fecha_registro', 'estado',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalle()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    public function cuenta_cobrar()
    {
        return $this->hasOne(CuentaCobrar::class, 'venta_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class, 'venta_id');
    }

    public static function eliminarVenta(Venta $venta)
    {
        DB::beginTransaction();
        try {
            if ($venta->cuenta_cobrar) {
                foreach ($venta->cuenta_cobrar->pagos as $cb_pago) {
                    $ingreso_caja = IngresoCaja::where("registro_id", $cb_pago->id)->where("tipo", "PAGO POR COBRAR")->get()->first();
                    if ($ingreso_caja) {
                        $ingreso_caja->delete();
                    }
                }
            }

            // recorrer el detalle de venta
            // registrar como INGRESO cada producto
            foreach ($venta->detalle as $value) {
                // BUSCAR EL DETALLE DEL LOTE
                // ARMAR LOS DETALLES STRINGS A ARRAYS
                $detalle_ingreso = DetalleIngreso::find($value->detalle_ingreso_id);
                // SI ES ANTICIPO RESTABLECER COMO DISPONIBLE EL STOCK DEL DETALLE
                if ($venta->tipo_venta == "ANTICIPOS" && $venta->saldo > 0) {
                    $detalle_ingreso->anticipo_kilos = (float)$detalle_ingreso->anticipo_kilos - (float)$value->cantidad_kilos;
                    $detalle_ingreso->anticipo = (float)$detalle_ingreso->anticipo - (float)$value->cantidad;
                    $detalle_ingreso->save();
                } else {
                    // incrementar stock lote-detalle
                    $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos + (float)$value->cantidad_kilos;
                    $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad + (float)$value->cantidad;
                    $detalle_ingreso->save();
                    // incrementar stock producto
                    $producto = $detalle_ingreso->producto;
                    $producto->stock_actual = (float)$producto->stock_actual + (float)$value->cantidad_kilos;
                    $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad + (float)$value->cantidad;
                    $producto->save();

                    $ultimo = KardexProducto::where('producto_id', $producto->id)
                        ->orderBy('created_at', 'asc')
                        ->get()
                        ->last();
                    KardexProducto::create([
                        'producto_id' => $producto->id,
                        'detalle_ingreso_id' => $detalle_ingreso->id,
                        'fecha' => date('Y-m-d'),
                        'detalle' => 'INGRESO POR DEVOLUCIÓN DE VENTA AL LOTE NRO. ' . $detalle_ingreso->ingreso_producto->nro_lote,
                        'tipo' => 'INGRESO',
                        'ingreso_c' => $value->cantidad_kilos,
                        'saldo_c' => (float)$ultimo->saldo_c + (float)$value->cantidad_kilos,
                        'cu' => $producto->precio,
                        'ingreso_m' => (float)$value->cantidad_kilos * (float)$producto->precio,
                        'saldo_m' => (float)$ultimo->saldo_m + ((float)$value->cantidad_kilos * (float)$producto->precio)
                    ]);
                }
            }

            if ($venta->cuenta_cobrar) {
                $venta->cuenta_cobrar->status = 0;
                $venta->cuenta_cobrar->save();
            }

            // anticipos
            $ingreso_caja = IngresoCaja::where("registro_id", $venta->id)->where("tipo", "ANTICIPO VENTA")->get()->first();
            if ($ingreso_caja) {
                $ingreso_caja->delete();
            }
            // cancelación de anticipos
            $ingreso_caja = IngresoCaja::where("registro_id", $venta->id)->where("tipo", "CANCELACIÓN DE ANTICIPO")->get()->first();
            if ($ingreso_caja) {
                $ingreso_caja->delete();
            }
            // al contado
            $ingreso_caja = IngresoCaja::where("registro_id", $venta->id)->where("tipo", "VENTA")->get()->first();
            if ($ingreso_caja) {
                $ingreso_caja->delete();
            }

            $venta->estado = 0;
            $venta->save();
            DB::commit();
            return 1;
        } catch (\Exception $e) {
            DB::rollBack();
            return 2;
        }
    }
}
