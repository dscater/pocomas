<?php

namespace App\Http\Controllers;

use App\Caja;
use Illuminate\Http\Request;
use App\Venta;
use App\VentaDetalle;
use App\Cliente;
use App\Producto;
use App\InicioCaja;
use App\CierreCaja;
use App\Concepto;
use App\Factura;
use App\KardexProducto;
use App\IngresoProducto;
use App\IngresoCaja;
use App\CuentaCobrar;
use App\DetalleIngreso;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;
use App\library\numero_a_letras\src\NumeroALetras;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = [];
        if (Auth::user()->tipo == 'CAJA') {
            $ventas = Venta::where('estado', 1)
                ->where('caja_id', Auth::user()->caja->caja_id)->get();
        } else if (Auth::user()->tipo == 'ADMINISTRADOR') {
            $ventas = Venta::where('estado', 1)->get();
        }
        return view('ventas.index', compact('ventas'));
    }

    public function anticipos()
    {
        $ventas = [];
        if (Auth::user()->tipo == 'CAJA') {
            $ventas = Venta::where("tipo_venta", "ANTICIPOS")->where('estado', 1)
                ->where('caja_id', Auth::user()->caja->caja_id)->get();
        } else if (Auth::user()->tipo == 'ADMINISTRADOR') {
            $ventas = Venta::where("tipo_venta", "ANTICIPOS")->where('estado', 1)->get();
        }
        return view('ventas.anticipos', compact('ventas'));
    }

    public function create()
    {
        // // validar cierre caja
        // $existe_cierre = CierreCaja::where('fecha_cierre', date('Y-m-d'))
        //     ->where('caja_id', Auth::user()->caja->caja_id)
        //     ->get()->first();
        // if ($existe_cierre) {
        //     return redirect()->back()->with('info', 'Esta caja ya cerró y no se pueden realizar ventas');
        // }

        $clientes = Cliente::where('estado', 1)->get();
        $productos = Producto::where('status', 1)
            ->where('estado', 'ACTIVO')
            ->where('status', 1)
            ->get();
        $array_clientes[''] = 'Buscar...';
        $array_productos[''] = "Seleccione...";

        foreach ($clientes as $value) {
            $array_clientes[$value->id] = $value->nombre;
        }
        foreach ($productos as $value) {
            $array_productos[$value->id] =  $value->nombre;
        }

        $conceptos = Concepto::all();
        $array_conceptos[""] = "Seleccione...";
        foreach ($conceptos as $value) {
            $array_conceptos[$value->id] = $value->nombre;
        }

        return view('ventas.create', \compact('array_clientes', 'array_productos', "array_conceptos"));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // VERIFICAR SI EXISTE UN INICIO DE CAJA
            $inicio_caja = InicioCaja::existeInicio(date('Y-m-d'), Auth::user()->caja->caja_id);
            if (!$inicio_caja) {
                $inicio_caja = InicioCaja::create([
                    'caja_id' => Auth::user()->caja->caja_id,
                    'monto_inicial' => (float)Caja::getSaldo(Auth::user()->caja->caja_id),
                    'fecha_inicio' => date('Y-m-d'),
                    'descripcion' => 'APERTURA DE CAJA POR VENTA EN CAJA',
                    'user_id' => Auth::user()->id,
                    'fecha_registro' => date('Y-m-d'),
                    'estado' => 1
                ]);
            }

            if ($request->tipo_venta == "ANTICIPOS") {
                $request["saldo"] = (float)$request["monto_total"] - (float)$request["anticipo"];
            } else {
                $request["anticipo"] = 0;
                $request["saldo"] = 0;
            }

            // registrar la venta
            if (
                $request->tipo_venta == "BANCO" ||
                $request->tipo_venta == "POR COBRAR" ||
                $request->tipo_venta == "ANTICIPOS"
            ) {
                $nueva_venta = new Venta(array_map('mb_strtoupper', [
                    'caja_id' => Auth::user()->caja->caja_id,
                    'user_id' => Auth::user()->id,
                    'cliente_id' => $request->cliente_id,
                    'cantidad_total' => $request->cantidad_total,
                    'anticipo' => $request->anticipo,
                    'saldo' => $request->saldo,
                    'monto_total' => $request->monto_total,
                    'tipo_venta' => $request->tipo_venta,
                    'fecha_venta' => date('Y-m-d'),
                    'hora_venta' => date('H:i:s'),
                    'fecha_registro' => date('Y-m-d'),
                    'estado' => 1
                ]));
            } else {
                $nueva_venta = new Venta(array_map('mb_strtoupper', [
                    'caja_id' => Auth::user()->caja->caja_id,
                    'user_id' => Auth::user()->id,
                    'cliente_id' => $request->cliente_id,
                    'cantidad_total' => $request->cantidad_total,
                    'anticipo' => $request->anticipo,
                    'saldo' => $request->saldo,
                    'monto_total' => $request->monto_total,
                    'tipo_venta' => $request->tipo_venta,
                    'fecha_venta' => date('Y-m-d'),
                    'hora_venta' => date('H:i:s'),
                    'monto_recibido' => $request->monto_recibido,
                    'monto_cambio' => $request->monto_cambio,
                    'fecha_registro' => date('Y-m-d'),
                    'estado' => 1
                ]));
            }
            $nueva_venta->save();

            // registrar el detalle
            $productos = $request->productos;
            $lote_id = $request->lote_id;
            $cantidad_lotes = $request->cantidad_lotes;
            $costos = $request->costos;
            $descuentos = $request->descuentos;
            $cantidads = $request->cantidads;
            $totales = $request->totales;
            for ($i = 0; $i < count($productos); $i++) {
                $producto = Producto::find($productos[$i]);

                $desc = 0;
                if (isset($descuentos[$i]) && $descuentos[$i] != "" && $descuentos[$i] != NULL) {
                    $desc  = $descuentos[$i];
                }

                $nuevo_detalle_venta = VentaDetalle::create([
                    'venta_id' => $nueva_venta->id,
                    'producto_id' => $productos[$i],
                    'detalle_ingreso_id' => $lote_id[$i],
                    'lotes_cantidades' => $cantidad_lotes[$i],
                    'cantidad' => $cantidads[$i],
                    'monto' => $costos[$i],
                    'descuento' => $desc,
                    'sub_total' => $totales[$i]
                ]);

                // VALIDAR SI EL TIPO DE VENTA ES ANTICIPOS: NO ACTUALIZAR EL STOCK
                // ARMAR LOS DETALLES STRINGS A ARRAYS
                $array_id_lotes = explode(",", $nuevo_detalle_venta->detalle_ingreso_id);
                $array_cantidad_lotes = explode(",", $nuevo_detalle_venta->lotes_cantidades);
                for ($k = 0; $k < count($array_id_lotes); $k++) {
                    $detalle_ingreso = DetalleIngreso::find($array_id_lotes[$k]);
                    if ($nueva_venta->tipo_venta != "ANTICIPOS") {
                        if ($detalle_ingreso->producto->medida == 'KILOS') {
                            // stock del DETALLE DEL LOTE
                            $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos - (float)$array_cantidad_lotes[$k];
                        } else {
                            $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad - (float)$array_cantidad_lotes[$k];
                        }
                        $detalle_ingreso->save();

                        // stock del PRODUCTO
                        $detalle_ingreso->producto->stock_actual = (float)$detalle_ingreso->producto->stock_actual - (float)$array_cantidad_lotes[$k];
                        $detalle_ingreso->producto->save();
                        // actualizar kardex
                        KardexProducto::registroEgreso($detalle_ingreso->producto, $array_cantidad_lotes[$k], $array_id_lotes[$k]);
                    } else {
                        $detalle_ingreso->anticipo = (float)$detalle_ingreso->anticipo + (float)$array_cantidad_lotes[$k];
                        $detalle_ingreso->save();
                    }
                }
            }

            // registrar factura
            $nro_factura = 1;
            $ultima_factura = Factura::orderBy('created_at', 'asc')->get()->last();
            if ($ultima_factura) {
                $nro_factura = (int)$ultima_factura->nro_factura + 1;
            }
            Factura::create([
                'venta_id' => $nueva_venta->id,
                'nro_factura' => $nro_factura,
                'cliente' => $nueva_venta->cliente->nombre,
                'nit' => $nueva_venta->cliente->ci
            ]);

            // registrar ingreso si es venta de tipo AL CONTADO
            $concepto_id = 0;
            // if ($nueva_venta->tipo_venta == 'AL CONTADO' || $nueva_venta->tipo_venta == "ANTICIPOS") {
            //     $concepto_id = $request->concepto_id;
            // }

            if ($nueva_venta->tipo_venta == 'AL CONTADO' || $nueva_venta->tipo_venta == "ANTICIPOS" || $nueva_venta->tipo_venta == "BANCO") {
                if ($request->tipo_venta == "ANTICIPOS") {
                    IngresoCaja::create([
                        'caja_id' => Auth::user()->caja->caja_id,
                        "inicio_caja_id" => $inicio_caja->id,
                        "concepto_id" => $concepto_id,
                        'tipo_movimiento' => 'INGRESO',
                        'tipo' => 'ANTICIPO VENTA',
                        'registro_id' => $nueva_venta->id,
                        'monto_total' => $nueva_venta->anticipo,
                        'fecha' => date('Y-m-d'),
                        'hora' => date('H:i:s'),
                        'estado' => 1,
                        "user_id" => Auth::user()->id
                    ]);
                } else {
                    // Log::debug($inicio_caja->id);
                    IngresoCaja::create([
                        'caja_id' => Auth::user()->caja->caja_id,
                        "inicio_caja_id" => $inicio_caja->id,
                        "concepto_id" => $concepto_id,
                        "tipo_movimiento" => "INGRESO",
                        'tipo' => 'VENTA',
                        'registro_id' => $nueva_venta->id,
                        'monto_total' => $nueva_venta->monto_total,
                        'fecha' => date('Y-m-d'),
                        'hora' => date('H:i:s'),
                        'estado' => 1,
                        "user_id" => Auth::user()->id
                    ]);
                }
            } else {
                // registrar cuenta por cobrar
                $cliente = Cliente::findOrFail($nueva_venta->cliente_id);
                $cuenta_cliente = $cliente->cuenta_cliente;
                if ($cuenta_cliente) {
                    $nuevo_monto = (float)$cuenta_cliente->total_deuda + (float)$nueva_venta->monto_total;
                    $nuevo_saldo = (float)$cuenta_cliente->saldo + (float)$nueva_venta->monto_total;
                    $cuenta_cliente->update([
                        "total_deuda" => $nuevo_monto,
                        "saldo" => $nuevo_saldo,
                        'estado' => 'PENDIENTE',
                    ]);
                } else {
                    $cuenta_cliente = $cliente->cuenta_cliente()->create([
                        "total_deuda" => $nueva_venta->monto_total,
                        "cancelado" => 0,
                        "saldo" => $nueva_venta->monto_total,
                        'estado' => 'PENDIENTE',
                    ]);
                }

                $nueva_cuenta_cobrar = $cuenta_cliente->cuenta_cobrars()->create([
                    'venta_id' => $nueva_venta->id,
                    'cliente_id' => $nueva_venta->cliente_id,
                    'monto_deuda' => $nueva_venta->monto_total,
                    'saldo' => $nueva_venta->monto_total,
                    'estado' => 'PENDIENTE',
                    'status' => 1
                ]);

                foreach ($nueva_venta->detalle as $value) {
                    $nueva_cuenta_cobrar->cuenta_cobrar_detalles()->create([
                        'venta_detalle_id' => $value->id,
                        'monto' => $value->sub_total,
                        'cancelado' => 0,
                        'saldo' => $value->sub_total,
                    ]);
                }
            }

            $url_orden_venta = route('ventas.show', $nueva_venta->id);
            DB::commit();
            return redirect()->route('ventas.index')->with('url_orden', $url_orden_venta)
                ->with('bien', 'Registro realizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ventas.index')->with('error', 'Ocurrió un error la venta no se llevó a cabo. ' . $e->getMessage());
        }
    }

    public function edit(Venta $venta)
    {
        $clientes = Cliente::where('estado', 1)->get();
        $array_clientes[''] = 'Seleccione...';
        foreach ($clientes as $value) {
            $array_clientes[$value->id] = $value->nombre;
        }
        return view('ventas.edit', compact('venta', 'array_clientes'));
    }

    public function update(Venta $venta, Request $request)
    {
        $venta->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('ventas.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Venta $venta)
    {
        $convertir = new NumeroALetras();
        $array_monto = explode('.', $venta->monto_total);
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1];
        $literal = strtolower($literal);
        $literal = ucfirst($literal) . "/100." . " Bolivianos";;
        $nro_factura = (int)$venta->factura->nro_factura;
        if ($nro_factura < 10) {
            $nro_factura = '000' . $nro_factura;
        } else if ($nro_factura < 100) {
            $nro_factura = '00' . $nro_factura;
        } else if ($nro_factura < 1000) {
            $nro_factura = '0' . $nro_factura;
        }
        return view("ventas.show", compact('venta', 'literal', 'nro_factura'));
    }

    public function confirmar_venta(Venta $venta)
    {
        $caja_id = $venta->caja_id;
        // VERIFICAR SI EXISTE UN INICIO DE CAJA
        $inicio_caja = InicioCaja::existeInicio(date('Y-m-d'), $caja_id);
        if (!$inicio_caja) {
            $inicio_caja = InicioCaja::create([
                'caja_id' => $caja_id,
                'monto_inicial' => (float)Caja::getSaldo($caja_id),
                'fecha_inicio' => date('Y-m-d'),
                'descripcion' => 'APERTURA DE CAJA POR VENTA EN CAJA',
                'user_id' => Auth::user()->id,
                'fecha_registro' => date('Y-m-d'),
                'estado' => 1
            ]);
        }
        foreach ($venta->detalle as $d) {
            $producto = Producto::find($d->producto_id);
            // stock del DETALLE DEL LOTE
            // ARMAR LOS DETALLES STRINGS A ARRAYS
            $array_id_lotes = explode(",", $d->detalle_ingreso_id);
            $array_cantidad_lotes = explode(",", $d->lotes_cantidades);
            for ($k = 0; $k < count($array_id_lotes); $k++) {
                $detalle_ingreso = DetalleIngreso::find($array_id_lotes[$k]);
                $detalle_ingreso->anticipo = (float)$detalle_ingreso->anticipo - (float)$array_cantidad_lotes[$k];

                if ($detalle_ingreso->producto->medida == 'KILOS') {
                    // stock del DETALLE DEL LOTE
                    $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos - (float)$array_cantidad_lotes[$k];
                } else {
                    $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad - (float)$array_cantidad_lotes[$k];
                }
                $detalle_ingreso->save();

                // stock del PRODUCTO
                $detalle_ingreso->producto->stock_actual = (float)$detalle_ingreso->producto->stock_actual - (float)$array_cantidad_lotes[$k];
                $detalle_ingreso->producto->save();
                // actualizar kardex
                KardexProducto::registroEgreso($detalle_ingreso->producto, $array_cantidad_lotes[$k], $array_id_lotes[$k]);
            }
        }

        // REGISTRAR SALDO A LA CAJA
        IngresoCaja::create([
            'caja_id' => $caja_id,
            "inicio_caja_id" => $inicio_caja->id,
            'concepto_id' => "0",
            "tipo_movimiento" => "INGRESO",
            'tipo' => 'CANCELACIÓN DE ANTICIPO',
            'registro_id' => $venta->id,
            'monto_total' => $venta->saldo,
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'estado' => 1,
            "user_id" => Auth::user()->id,
        ]);

        $venta->anticipo = $venta->monto_total;
        $venta->saldo = 0;
        $venta->save();
        return redirect()->route('ventas.index')->with('bien_swal', 'La venta se confirmó exitosamente');
    }

    public function destroy(Venta $venta)
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
                $array_id_lotes = explode(",", $value->detalle_ingreso_id);
                $array_cantidad_lotes = explode(",", $value->lotes_cantidades);
                for ($k = 0; $k < count($array_id_lotes); $k++) {
                    $detalle_ingreso = DetalleIngreso::find($array_id_lotes[$k]);
                    // SI ES ANTICIPO RESTABLECER COMO DISPONIBLE EL STOCK DEL DETALLE
                    if ($venta->tipo_venta == "ANTICIPOS" && $venta->saldo > 0) {
                        $detalle_ingreso->anticipo = (float)$detalle_ingreso->anticipo - (float)$array_cantidad_lotes[$k];
                        $detalle_ingreso->save();
                    } else {
                        // incrementar stock lote-detalle
                        if ($detalle_ingreso->producto->medida == 'KILOS') {
                            // stock del DETALLE DEL LOTE
                            $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos + (float)$array_cantidad_lotes[$k];
                        } else {
                            $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad + (float)$array_cantidad_lotes[$k];
                        }
                        $detalle_ingreso->save();
                        // incrementar stock producto
                        $producto = $detalle_ingreso->producto;
                        $producto->stock_actual = (float)$producto->stock_actual + (float)$array_cantidad_lotes[$k];
                        $producto->save();

                        $ultimo = KardexProducto::where('producto_id', $producto->id)
                            ->orderBy('created_at', 'asc')
                            ->get()
                            ->last();
                        KardexProducto::create([
                            'producto_id' => $producto->id,
                            'detalle_ingreso_id' => $array_id_lotes[$k],
                            'fecha' => date('Y-m-d'),
                            'detalle' => 'INGRESO POR DEVOLUCIÓN DE VENTA AL LOTE NRO. ' . $detalle_ingreso->ingreso_producto->nro_lote,
                            'tipo' => 'INGRESO',
                            'ingreso_c' => $array_cantidad_lotes[$k],
                            'saldo_c' => (float)$ultimo->saldo_c + (float)$array_cantidad_lotes[$k],
                            'cu' => $producto->precio,
                            'ingreso_m' => (float)$array_cantidad_lotes[$k] * (float)$producto->precio,
                            'saldo_m' => (float)$ultimo->saldo_m + ((float)$array_cantidad_lotes[$k] * (float)$producto->precio)
                        ]);
                    }
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
            return redirect()->route('ventas.index')->with('bien', 'Registro eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ventas.index')->with('error', $e->getMessage());
        }
    }

    public function orden_venta(Venta $venta)
    {
        $convertir = new NumeroALetras();
        $array_monto = explode('.', $venta->monto_total);
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";
        $literal = strtolower($literal);
        $literal = ucfirst($literal);
        $nro_factura = (int)$venta->factura->nro_factura;
        if ($nro_factura < 10) {
            $nro_factura = '000' . $nro_factura;
        } else if ($nro_factura < 100) {
            $nro_factura = '00' . $nro_factura;
        } else if ($nro_factura < 1000) {
            $nro_factura = '0' . $nro_factura;
        }

        $pdf = PDF::loadView('ventas.orden_venta', compact('venta', 'literal', 'nro_factura'))->setPaper('letter', 'landscape');
        return $pdf->stream('OrdeVenta.pdf');
    }
}
