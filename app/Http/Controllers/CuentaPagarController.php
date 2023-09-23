<?php

namespace App\Http\Controllers;

use App\CajaCentral;
use App\CuentaPagar;
use App\IngresoProducto;
use App\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CuentaPagarController extends Controller
{
    public function index()
    {
        $cuenta_pagars = CuentaPagar::orderBy('created_at', 'desc')->get();
        return view("cuenta_pagars.index", compact("cuenta_pagars"));
    }

    public function create()
    {
        $ingreso_productos = IngresoProducto::where("saldo", ">", 0)
            ->orderBy('nro_lote', 'asc')
            ->get();
        $array_ingreso_productos[""] = "Seleccione...";
        foreach ($ingreso_productos as $value) {
            $array_ingreso_productos[$value->id] = $value->nro_lote;
        }

        $proveedors = Proveedor::select("proveedors.*")
            ->join("cuenta_pagars", "cuenta_pagars.proveedor_id", "=", "proveedors.id")
            ->where("cuenta_pagars.saldo", ">", 0)
            ->distinct("proveedors.id")
            ->orderBy('razon_social', 'asc')->get();
        $array_proveedors[""] = "Seleccione...";
        foreach ($proveedors as $value) {
            $array_proveedors[$value->id] = $value->razon_social;
        }

        return view("cuenta_pagars.create", compact("array_ingreso_productos", "array_proveedors"));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->data;
            foreach ($data as $value) {
                $ingreso_producto = IngresoProducto::findOrFail($value["id"]);
                $cuenta_pagar = CuentaPagar::where("ingreso_producto_id", $value["id"])->get()->first();
                // Log::debug($value["id"]);

                // $saldo_inicial = $cuenta_pagar->saldo;
                $saldo = (float)$value["saldo"];
                $nuevo_saldo = (float)$value["nuevo_saldo"];
                $total_pagado = $saldo;
                // Log::debug("NUEVO SALDO: " . $nuevo_saldo);
                // Log::debug("SALDO: " . $saldo);
                if ($cuenta_pagar) {
                    if ($nuevo_saldo > 0) {
                        $total_pagado = $saldo - $nuevo_saldo;
                    }
                    $cuenta_pagar->saldo = (float)$cuenta_pagar->saldo - (float)$total_pagado;
                    $cuenta_pagar->save();
                } else {
                    $request["fecha_registro"] = date("Y-m-d");
                    $cuenta_pagar = CuentaPagar::create([
                        "ingreso_producto_id" => $ingreso_producto->id,
                        "proveedor_id" => $ingreso_producto->proveedor_id,
                        "monto_total" => $ingreso_producto->precio_total,
                        "saldo" => $nuevo_saldo,
                        "descripcion" => "",
                        "fecha_registro" => $ingreso_producto->fecha_registro
                    ]);
                    if ($nuevo_saldo > 0) {
                        $total_pagado = $saldo - $nuevo_saldo;
                    }
                }

                $cuenta_pagar->ingreso_producto->saldo = $cuenta_pagar->saldo;
                $cuenta_pagar->ingreso_producto->save();

                if ($total_pagado > 0) {
                    $cuenta_pagar_detalle = $cuenta_pagar->cuenta_pagar_detalles()->create([
                        "monto" => $total_pagado,
                        "saldo" => $cuenta_pagar->saldo,
                        "total" => $cuenta_pagar->monto_total,
                        'tipo_pago' => $request->tipo_pago,
                        "descripcion" => mb_strtoupper($request->descripcion),
                        "fecha" => date("Y-m-d")
                    ]);
                    // Log::debug("total PAGADO: " . $total_pagado);
                    CajaCentral::create([
                        "fecha" => date("Y-m-d"),
                        'monto' => $total_pagado,
                        'concepto_id' => 0,
                        'cuenta_pagar_id' => $cuenta_pagar_detalle->id,
                        'descripcion' => mb_strtoupper($request->descripcion),
                        'tipo' => 'EGRESO',
                        'tipo_transaccion' => $request->tipo_pago,
                        'sw_egreso' => 'COMPRA',
                        "fecha_registro" => date("Y-m-d"),
                    ]);
                }

                // return redirect()->route('cuenta_pagars.index')->with('bien', 'Registro realizado con éxito');
            }
            DB::commit();
            return response()->JSON([
                "sw" => true,
                "url_lista" => route("cuenta_pagars.index")
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                "sw" => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function edit(CuentaPagar $cuenta_pagar)
    {
        $ingreso_productos = IngresoProducto::where("saldo", ">", 0)
            ->whereOr("id", $cuenta_pagar->id)
            ->orderBy('nro_lote', 'asc')
            ->get();
        $array_ingreso_productos[""] = "Seleccione...";
        foreach ($ingreso_productos as $value) {
            $array_ingreso_productos[$value->id] = $value->nro_lote;
        }

        $proveedors = Proveedor::orderBy('razon_social', 'asc')->get();
        $array_proveedors[""] = "Seleccione...";
        foreach ($proveedors as $value) {
            $array_proveedors[$value->id] = $value->razon_social;
        }
        return view("cuenta_pagars.edit", compact("cuenta_pagar", "array_ingreso_productos", "array_proveedors"));
    }
    public function update(CuentaPagar $cuenta_pagar, Request $request)
    {
        $cuenta_pagar->saldo = (float)$cuenta_pagar->ingreso_producto->saldo - (float)$request->monto_total;
        $cuenta_pagar->save();

        $cuenta_pagar->ingreso_producto->saldo = $cuenta_pagar->saldo;
        $cuenta_pagar->ingreso_producto->save();

        $cuenta_pagar_detalle = $cuenta_pagar->cuenta_pagar_detalles()->create([
            "monto" => $request->monto_total,
            "saldo" => $cuenta_pagar->saldo,
            "total" => $cuenta_pagar->monto_total,
            'tipo_pago' => $request->tipo_pago,
            "descripcion" => mb_strtoupper($request->descripcion),
            "fecha" => date("Y-m-d")
        ]);

        CajaCentral::create([
            "fecha" => date("Y-m-d"),
            'monto' => $request->monto_total,
            'concepto_id' => 0,
            'cuenta_pagar_id' => $cuenta_pagar_detalle->id,
            'descripcion' => mb_strtoupper($request->descripcion),
            // 'descripcion' => 'CUENTA POR PAGAR, COMPRA DE PRODUCTOS',
            'tipo' => 'EGRESO',
            "tipo_transaccion" => "CAJA",
            'sw_egreso' => 'COMPRA',
            "fecha_registro" => date("Y-m-d"),
        ]);

        return redirect()->route('cuenta_pagars.index')->with('bien', 'Registro actualizado');
    }

    public function show(CuentaPagar $cuenta_pagar)
    {
        return view("cuenta_pagars.show", compact("cuenta_pagar"));
    }

    public function destroy(CuentaPagar $cuenta_pagar)
    {
        $cuenta_pagar->ingreso_producto->saldo = (float)$cuenta_pagar->ingreso_producto->saldo + (float)$cuenta_pagar->monto_total;
        $cuenta_pagar->ingreso_producto->save();
        $cuenta_pagar->delete();
        return redirect()->route('cuenta_pagars.index')->with('bien', 'Registro eliminado');
    }
}
