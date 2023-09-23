<?php

namespace App\Http\Controllers;

use App\Caja;
use App\CajaCentral;
use Illuminate\Http\Request;
use App\CuentaCobrar;
use App\CuentaPago;
use App\Cliente;
use App\CuentaCliente;
use App\IngresoCaja;
use App\InicioCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\library\numero_a_letras\src\NumeroALetras;

class CuentaCobrarController extends Controller
{
    public function index()
    {
        $cuenta_cobrars = CuentaCliente::all();
        return view('cuenta_cobrars.index', compact('cuenta_cobrars'));
    }

    public function pagos(CuentaCliente $cuenta_cliente)
    {
        return view('cuenta_cobrars.pagos', compact('cuenta_cliente'));
    }

    public function create()
    {
        $clientes = Cliente::select("clientes.*")
            ->join("cuenta_clientes", "cuenta_clientes.cliente_id", "=", "clientes.id")
            ->where("cuenta_clientes.total_deuda", ">", 0)
            ->where('clientes.estado', 1)->get();
        $array_clientes[''] = 'Seleccione...';
        foreach ($clientes as $value) {
            $array_clientes[$value->id] = $value->nombre;
        }

        $cajas = Caja::where('estado', 1)->get();
        $array_cajas[''] = "Seleccione...";
        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }
        return view('cuenta_cobrars.create', compact('array_clientes', 'array_cajas'));
    }

    public function registrarPago(Request $request)
    {
        DB::beginTransaction();
        try {
            // VERIFICAR SI EXISTE UN INICIO DE CAJA
            $caja_id = null;
            if (Auth::user()->caja) {
                $caja_id = Auth::user()->caja->caja_id;
            }
            if ($caja_id) {
                $inicio_caja = InicioCaja::existeInicio(date('Y-m-d'), $caja_id);
                if (!$inicio_caja) {
                    $inicio_caja = InicioCaja::create([
                        'caja_id' => $caja_id,
                        'monto_inicial' => (float)Caja::getSaldo($caja_id),
                        'fecha_inicio' => date('Y-m-d'),
                        'descripcion' => 'APERTURA DE CAJA POR INGRESOS',
                        'user_id' => Auth::user()->id,
                        'fecha_registro' => date('Y-m-d'),
                        'estado' => 1
                    ]);
                }
            }

            $cliente = Cliente::findOrFail($request->cliente_id);
            $cuenta_cliente = $cliente->cuenta_cliente;
            $total_cancelado = (float)$request->total_cancelado;

            $cuenta_cobrars = CuentaCobrar::where("cuenta_id", $cuenta_cliente->id)
                ->where('estado', 'PENDIENTE')
                ->where("status", 1)
                ->orderBy('created_at', 'asc')
                ->get();

            // asignar un monto_restante para ir recorriendo y pagando cada cuenta por cobrar
            // hasta que el monto RESTANTE quede en 0
            $monto_restante = $total_cancelado;
            foreach ($cuenta_cobrars as $cc) {
                $saldo = (float)$cc->saldo;
                $nuevo_saldo = 0;
                // almacenar en un auxiliar el monto que se pagara en cada cuenta por cobrar
                // para la actualización de montos de los detalles de cuentas por cobrar
                $aux_cancelado = $saldo;
                if ($saldo > $monto_restante) {
                    $aux_cancelado = $monto_restante;
                    $nuevo_saldo = $saldo - $monto_restante;
                    $monto_restante = 0;
                } else {
                    $monto_restante = $monto_restante - $saldo;
                }

                if ($nuevo_saldo == 0) {
                    $cc->estado = "CANCELADO";
                }

                $cc->saldo = $nuevo_saldo;
                $cc->save();

                // ACTUALIZAR LOS DETALLES DE CUENTAS POR COBRAR
                // UTILIZANDO EL AUX_CANCELADO
                $monto_restante_detalle = $aux_cancelado;
                foreach ($cc->cuenta_cobrar_detalles as $ccd) {
                    $ccd_monto = $ccd->monto;
                    $ccd_cancelado = $ccd->cancelado;
                    $ccd_saldo = $ccd->saldo;
                    $saldo_actualizado_detalle = 0;
                    if ($ccd_saldo > $monto_restante_detalle) {
                        $saldo_actualizado_detalle = $ccd_saldo - $monto_restante_detalle;
                        $ccd->cancelado = (float)$ccd_cancelado + (float)$monto_restante_detalle;
                        $monto_restante_detalle = 0;
                    } else {
                        $monto_restante_detalle = $monto_restante_detalle - $ccd_saldo;
                        $ccd->cancelado = $ccd_monto;
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

            // REGISTRAR EL PAGO
            $pago = $cuenta_cliente->cuenta_pagos()->create([
                'caja_id' => $caja_id,
                'monto' => $total_cancelado,
                'observacion' => mb_strtoupper($request->observacion),
                "tipo_cobro" => $request->tipo_cobro,
                'fecha_pago' => date('Y-m-d')
            ]);

            // ACTUALIZAR MONTOS DE LA CUENTA DEL CLIENTE
            $suma_total_cancelado = (float)$cuenta_cliente->cancelado + $total_cancelado;
            $cuenta_cliente->cancelado = $suma_total_cancelado;
            $nuevo_saldo_cuenta = (float)$cuenta_cliente->total_deuda - $suma_total_cancelado;
            $cuenta_cliente->saldo = $nuevo_saldo_cuenta;
            if ($nuevo_saldo_cuenta == 0) {
                $cuenta_cliente->estado = "CANCELADO";
            }
            $cuenta_cliente->save();

            if ($caja_id) {
                IngresoCaja::create([
                    'caja_id' => $caja_id,
                    "inicio_caja_id" => $inicio_caja->id,
                    'concepto_id' => 0,
                    'tipo_movimiento' => 'INGRESO',
                    'tipo' => 'PAGO POR COBRAR',
                    'registro_id' => $pago->id,
                    'monto_total' => $total_cancelado,
                    'fecha' => date('Y-m-d'),
                    'hora' => date('H:i:s'),
                    'estado' => 1,
                    "user_id" => Auth::user()->id
                ]);
            } else {
                // REGISTRAR EN CAJA CENTRAL COMO INGRESO
                CajaCentral::create([
                    'fecha' => date("Y-m-d"),
                    'monto' => $total_cancelado,
                    'descripcion' => 'INGRESO POR CUENTA POR COBRAR',
                    "concepto_id" => 0,
                    'tipo' => "INGRESO",
                    "tipo_transaccion" => $request->tipo_cobro,
                    'fecha_registro' => date('Y-m-d'),
                ]);
            }

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'url_comprobante' => route('cuenta_cobrars.comprobante', $cuenta_cliente->id) . '?imprime=true',
                "url_listado" => route("cuenta_cobrars.index")
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDetalleOrden(Request $request)
    {
        $cuenta_cobrar = CuentaCobrar::find($request->cci);
        $nro_factura = (int)$cuenta_cobrar->venta->factura->nro_factura;
        if ($nro_factura < 10) {
            $nro_factura = '000' . $nro_factura;
        } else if ($nro_factura < 100) {
            $nro_factura = '00' . $nro_factura;
        } else if ($nro_factura < 1000) {
            $nro_factura = '0' . $nro_factura;
        }

        $html = '<div class="contenedor_orden" style="overflow: auto;">
                <div class="titulo_orden">
                    ORDEN DE VENTA
                </div>
                <div class="elemento detalle izquierda">
                    Nro: ' . $nro_factura . ' <br>
                    Fecha: ' . date('d/m/Y', strtotime($cuenta_cobrar->venta->fecha_venta)) . ' <br>
                    Hora: ' . $cuenta_cobrar->venta->hora_venta . ' <br>
                    Cliente: ' . $cuenta_cobrar->venta->factura->cliente . ' <br>
                    CI/NIT: ' . $cuenta_cobrar->venta->factura->nit . ' <br>
                    Caja: ' . $cuenta_cobrar->venta->user->name . ' <br>
                    Tipo de Pago: ' . $cuenta_cobrar->venta->tipo_venta . ' <br>
                </div>
                <div class="titulo_detalle">
                    DETALLE
                </div>
                <div class="cobro" style="width:100%; overflow: auto;">
                    <table class="table table-bordered">
                        <tr class="punteado bg-danger">
                            <td class="centreado">CANTIDAD</td>
                            <td class="centreado">PRODUCTO</td>
                            <td class="centreado">CU</td>
                            <td class="centreado">TOTAL S/D</td>
                            <td class="centreado">DESCUENTO</td>
                            <td class="centreado">TOTAL</td>
                        </tr>';
        foreach ($cuenta_cobrar->venta->detalle as $value) {
            $html .= '<tr>
                        <td class="centreado">' . $value->cantidad . '</td>
                        <td class="izquierda">' . $value->producto->nombre . '</td>
                        <td class="centreado">' . $value->producto->precio . '</td>
                        <td class="centreado">' . $value->total_sd . '</td>
                        <td class="centreado">' . $value->descuento . '</td>
                        <td class="centreado">' . $value->sub_total . '</td>
                    </tr>';
        }
        $html .= '<tr class="totales">
                        <td colspan="6" class="bold elemento">Total Final:
                            ' . $cuenta_cobrar->venta->monto_total . '</td>
                    </tr>
                    <tr class="totales">
                            <td colspan="6" class="bold elemento">Saldo Pendiente:
                                ' . $cuenta_cobrar->saldo . '</td>
                        </tr>
                    </table>
                </div>
                </div>';

        // LISTADO DE PAGOS
        $html_lista = "";
        $pagos = $cuenta_cobrar->pagos;
        $html_lista = '<div class="contenedor_orden" style="overflow: auto;">
        <div class="titulo_orden">
            LISTA DE PAGOS REALIZADOS
        </div>
        <div class="cobro" style="width:100%; overflow: auto;">
            <table class="table table-bordered">
                <tr class="punteado bg-danger">
                    <td class="centreado">NRO.</td>
                    <td class="centreado">CAJA</td>
                    <td class="centreado">MONTO</td>
                    <td class="centreado">OBSERVACIÓN</td>
                    <td class="centreado">FECHA</td>
                </tr>';
        $cont = 1;
        if (count($pagos) > 0)
            foreach ($pagos as $value) {
                $html_lista .= '<tr>
                    <td class="centreado">' . $cont++ . '</td>
                    <td class="centreado">' . $value->caja->nombre . '</td>
                    <td class="centreado">' . $value->monto . '</td>
                    <td class="centreado">' . $value->observacion . '</td>
                    <td class="centreado">' . date("d/m/Y", strtotime($value->fecha_pago)) . '</td>
                </tr>';
            }
        else {
            $html_lista .= '<tr>
                            <td class="centreado text-gray" colspan="5"><i>SIN REGISTROS AÚN</i></td>
                        </tr>';
        }

        $monto_deuda = $cuenta_cobrar->monto_deuda;
        $saldo = $cuenta_cobrar->saldo;

        // CUENTA COBRAR DETALLES
        $cuenta_cobrar_detalles = view("parcial.cuenta_cobrar_detalles", compact("cuenta_cobrar"))->render();

        return response()->JSON([
            'sw' => true,
            'html' => $html,
            'html_lista' => $html_lista,
            'cuenta_cobrar_detalles' => $cuenta_cobrar_detalles,
            'saldo' => $saldo,
        ]);
    }

    public function comprobante(CuentaCliente $cuenta_cliente)
    {
        $convertir = new NumeroALetras();
        $array_monto = explode('.', $cuenta_cliente->ultimo_pago->monto);
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";

        return view('cuenta_cobrars.comprobante', compact('cuenta_cliente', 'literal'));
        // $pdf = PDF::loadView('cuenta_cobrars.comprobante', compact('cuenta_cobrar', 'literal'))->setPaper('letter', 'landscape');
        // return $pdf->stream('Comprobante.pdf');
    }
}
