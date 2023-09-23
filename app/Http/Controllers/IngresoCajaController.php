<?php

namespace App\Http\Controllers;

use App\Caja;
use App\CajaCentral;
use App\Concepto;
use App\IngresoCaja;
use App\InicioCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngresoCajaController extends Controller
{
    public function index(Caja $caja)
    {
        $ingreso_cajas = IngresoCaja::where('caja_id', $caja->id)->where("estado", 1)->orderBy('created_at', 'desc')->get();
        $saldo_actual = number_format(Caja::getSaldo($caja->id), 2);
        $suma_bancos = number_format(Caja::getSumaBancos($caja->id), 2);
        $suma_otros = number_format(Caja::getSumaOtros($caja->id), 2);
        return view("ingreso_cajas.index", compact("ingreso_cajas", "saldo_actual", "suma_bancos", "suma_otros", "caja"));
    }

    public function create(Caja $caja)
    {
        $conceptos = Concepto::all();
        $array_conceptos[""] = "Seleccione...";
        foreach ($conceptos as $value) {
            $array_conceptos[$value->id] = $value->nombre;
        }
        return view("ingreso_cajas.create", compact('caja', "array_conceptos"));
    }

    public function store(Request $request)
    {
        $caja_id = $request->caja_id;
        if (!isset($caja_id) || !$caja_id || $caja_id == "" || $caja_id == null) {
            return redirect()->route('ingreso_cajas.create', $request->caja_id)->with('error_swal', 'Error, ocurrió un error al intentar realizar el registro, por favor intente nuevamente');
        }

        $inicio_caja = InicioCaja::existeInicio(date('Y-m-d'), $caja_id);
        if (!$inicio_caja) {
            $inicio_caja = InicioCaja::create([
                'caja_id' => $caja_id,
                'monto_inicial' => (float)Caja::getSaldo($caja_id),
                'fecha_inicio' => date('Y-m-d'),
                'descripcion' => 'APERTURA DE CAJA POR INGRESOS Y EGRESOS',
                'user_id' => Auth::user()->id,
                'fecha_registro' => date('Y-m-d'),
                'estado' => 1
            ]);
        }

        if ($request->tipo_movimiento == "EGRESO" && Caja::getSaldo($request->caja_id) < (float)$request->monto_total) {
            return redirect()->route('ingreso_cajas.create', $request->caja_id)->with('error_swal', 'No es posible realizar un Egreso, debido a que el saldo actual es de ' . number_format(Caja::getSaldo($request->caja_id), 2));
        }

        // CAJA CENTRAL
        // $saldo_central = CajaCentral::getSaldo();
        // if ($saldo_central <= 0) {
        //     return redirect()->route('ingreso_cajas.create', $request->caja_id)->with('error_swal', 'No es posible realizar un Ingreso, debido a que el saldo de la CAJA CENTRAL es de ' . number_format($saldo_central, 2));
        // }

        $request["hora"] = date("H:i:s");
        $request["estado"] = 1;
        if ($request->tipo_movimiento == "EGRESO") {
            $request["sw_egreso"] = "GASTO";
        }
        $request["inicio_caja_id"] = $inicio_caja->id;
        $request["user_id"] = Auth::user()->id;
        $ingreso_caja = IngresoCaja::create(array_map("mb_strtoupper", $request->all()));

        if ($request->tipo_movimiento == "INGRESO") {
            // VERIFICAR SI EXISTE UN INICIO DE CAJA
            $inicio_caja = InicioCaja::existeInicio(date('Y-m-d'), $request->caja_id);
            if (!$inicio_caja) {
                $inicio_caja = InicioCaja::create([
                    'caja_id' => $request->caja_id,
                    'monto_inicial' => $request->monto_total,
                    'fecha_inicio' => date('Y-m-d'),
                    'descripcion' => 'APERTURA DE CAJA POR INGRESOS',
                    'user_id' => Auth::user()->id,
                    'fecha_registro' => date('Y-m-d'),
                    'estado' => 1
                ]);
            }

            // CajaCentral::create([
            //     "fecha" => $ingreso_caja->fecha,
            //     "monto" => $ingreso_caja->monto_total,
            //     "descripcion" => "EGRESO DE " . $ingreso_caja->monto_total . " HACIA LA CAJA " . $ingreso_caja->caja->nombre,
            //     "concepto_id" => $request->concepto_id,
            //     "tipo" => "EGRESO",
            //     "fecha_registro" => date("Y-m-d")
            // ]);
        } else {
            // if ($ingreso_caja->sw_egreso == 'EGRESO A CAJA CENTRAL') {
            //     CajaCentral::create([
            //         "fecha" => $ingreso_caja->fecha,
            //         "monto" => $ingreso_caja->monto_total,
            //         "descripcion" => "INGRESO DE " . $ingreso_caja->monto_total . " DESDE LA CAJA " . $ingreso_caja->caja->nombre,
            //         "concepto_id" => $request->concepto_id,
            //         "tipo" => "INGRESO",
            //         "fecha_registro" => date("Y-m-d")
            //     ]);
            // }
        }

        return redirect()->route('ingreso_cajas.index', $request->caja_id)->with('bien', 'Registro realizado con éxito');
    }

    public function edit(IngresoCaja $ingreso_caja)
    {
        $conceptos = Concepto::all();
        $array_conceptos[""] = "Seleccione...";
        foreach ($conceptos as $value) {
            $array_conceptos[$value->id] = $value->nombre;
        }
        return view("ingreso_cajas.edit", compact("ingreso_caja", "array_conceptos"));
    }

    public function show(IngresoCaja $ingreso_caja)
    {
        return view("ingreso_cajas.show", compact("ingreso_caja"));
    }
    public function update(IngresoCaja $ingreso_caja, Request $request)
    {
        $ingreso_caja->update(array_map("mb_strtoupper", $request->all()));
        return redirect()->route('ingreso_cajas.index', $ingreso_caja->caja->id)->with('bien', 'Registro actualizado');
    }

    public function destroy(IngresoCaja $ingreso_caja)
    {
        $caja = $ingreso_caja->caja;
        if ($ingreso_caja->estado == 1) {
            $resp = IngresoCaja::eliminarRegistro($ingreso_caja);
            if ($resp == 1) {
                return redirect()->route('ingreso_cajas.index', $caja->id)->with('bien', 'Registro eliminado');
            }
            return redirect()->route('ingreso_cajas.index', $caja->id)->with('error', "Ocurrió un error al intentar eliminar el registro.");
        }
        return redirect()->route('ingreso_cajas.index', $caja->id)->with('error', 'No es posible eliminar el registro, debido a que la caja ya se cerró');
    }
}
