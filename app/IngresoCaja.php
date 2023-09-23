<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngresoCaja extends Model
{
    protected $fillable = [
        'caja_id', 'inicio_caja_id', 'tipo_movimiento', 'tipo', 'registro_id', 'monto_total', 'concepto_id', 'fecha', 'hora', 'sw_egreso', 'estado', "user_id"
    ];

    protected $appends = ["descripcion_txt"];

    public function getDescripcionTxtAttribute()
    {
        if ($this->tipo == 'VENTA') {
            $venta = Venta::where("id", $this->registro_id)->get()->first();
            if ($venta) {
                return $this->tipo . ' (' . $venta->tipo_venta . ')';
            }
        }
        if ($this->tipo == 'PAGO POR COBRAR') {
            $cuenta_pago = CuentaPago::where("id", $this->registro_id)->get()->first();
            if ($cuenta_pago) {
                return $this->tipo . ' (' . $cuenta_pago->tipo_cobro . ')';
            }
        }
        return $this->tipo;
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'registro_id');
    }

    public function cuenta_pago()
    {
        return $this->belongsTo(CuentaPago::class, 'registro_id');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'concepto_id');
    }

    public static function eliminarRegistro(IngresoCaja $ingreso_caja)
    {
        if (
            $ingreso_caja->tipo == 'VENTA' ||
            $ingreso_caja->tipo == 'CANCELACIÓN DE ANTICIPO' ||
            $ingreso_caja->tipo == 'ANTICIPO VENTA'
        ) {

            if ($ingreso_caja->venta) {
                // Venta
                $venta = $ingreso_caja->venta;
                if ($ingreso_caja->tipo == 'CANCELACIÓN DE ANTICIPO') {
                    // restaurar saldo actual
                    $anticipo =  (float)$venta->monto_total - (float)$ingreso_caja->monto_total;
                    $venta->anticipo = $anticipo;
                    $saldo = (float)$venta->monto_total - (float)$anticipo;
                    $venta->saldo = $saldo;
                    $venta->save();
                    $ingreso_caja->delete();
                    return 1;
                } else {
                    $res_venta = Venta::eliminarVenta($venta);
                    if ($res_venta == 1) {
                        $ingreso_caja->delete();
                        return 1;
                    }
                }
            }
            // no existe  venta
            return 2;
        } elseif ($ingreso_caja->tipo == 'PAGO POR COBRAR') {
            // CuentaPago
            $cuenta_pago = CuentaPago::find($ingreso_caja->registro_id);
            if ($cuenta_pago) {
                $cuenta_cliente = $cuenta_pago->cuenta_cliente;
                $cuenta_cliente->saldo = (float)$cuenta_pago->monto + (float)$cuenta_cliente->saldo;
                $cuenta_cliente->cancelado = (float)$cuenta_cliente->cancelado - (float)$cuenta_pago->monto;
                $cuenta_cliente->estado = "PENDIENTE";
                $cuenta_cliente->save();

                $cuenta_cobrars = CuentaCobrar::where("cuenta_id", $cuenta_cliente->id)
                    ->whereIn('estado', ['PENDIENTE', 'CANCELADO'])
                    ->where("monto_deuda", "!=", "saldo")
                    ->where("status", 1)
                    ->orderBy('created_at', 'desc')
                    ->get();

                // asignar un monto_restante para ir recorriendo y sumando cada cuenta por cobrar
                // hasta que el monto RESTANTE se restablezca con el monto eliminado
                $monto_restante = $ingreso_caja->monto_total;
                foreach ($cuenta_cobrars as $cc) {
                    $saldo_sumado = (float)$cc->saldo + (float)$monto_restante;
                    $saldo = (float)$cc->saldo;
                    $monto_deuda = (float) $cc->monto_deuda;
                    $nuevo_saldo = $cc->monto_deuda;
                    // almacenar en un auxiliar el monto que se descontara en cada cuenta por cobrar
                    // para la actualización de montos de los detalles de cuentas por cobrar
                    $monto_devolucion = $monto_deuda - $saldo;
                    $aux_monto_devolucion = $monto_devolucion;
                    if ($saldo_sumado > $monto_deuda) {
                        $monto_restante = $saldo_sumado - $monto_deuda;
                    } else {
                        $nuevo_saldo = $monto_restante + $saldo;
                        $monto_devolucion = $monto_restante;
                        $aux_monto_devolucion = $monto_devolucion;
                        $monto_restante = 0;
                    }

                    if ($nuevo_saldo > 0) {
                        $cc->estado = "PENDIENTE";
                    }
                    $cc->saldo = $nuevo_saldo;
                    $cc->save();

                    // ACTUALIZAR LOS DETALLES DE CUENTAS POR COBRAR
                    // UTILIZANDO EL AUX_CANCELADO
                    $monto_restante_detalle = $aux_monto_devolucion;
                    $cuenta_cobrar_detalles = CuentaCobrarDetalle::where("cuenta_cobrar_id", $cc->id)->orderBy("venta_detalle_id", "desc")->get();
                    foreach ($cuenta_cobrar_detalles as $ccd) {
                        $ccd_monto = $ccd->monto;
                        $ccd_saldo_sumado = (float)$ccd->saldo + (float)$monto_restante_detalle;
                        $ccd_saldo = $ccd->saldo;
                        $saldo_actualizado_detalle = $ccd_monto;
                        if ($ccd_saldo_sumado > $ccd_monto) {
                            $monto_restante_detalle = $ccd_saldo_sumado - $ccd_monto;
                            $ccd->cancelado = 0;
                        } else {
                            $saldo_actualizado_detalle = $monto_restante_detalle + $ccd_saldo;
                            $ccd->cancelado = (float)$ccd_monto - (float)$ccd_saldo_sumado;
                            $monto_restante_detalle = 0;
                        }
                        $ccd->saldo = $saldo_actualizado_detalle;
                        $ccd->save();
                        if ($monto_restante_detalle <= 0) {
                            break;
                        }
                    }
                    if ($monto_restante <= 0) {
                        // si ya no queda mas para registrar salirse del for
                        break;
                    }
                }

                if ($cuenta_pago->delete()) {
                    $ingreso_caja->delete();
                    return 1;
                }
            }
            return 2;
        } else {
            // ingreso/egreso normal
            if ($ingreso_caja->delete()) {
                return 1;
            }
        }

        return 2;
    }
}
