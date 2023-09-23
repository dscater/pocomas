<?php

namespace App\Http\Controllers;

use App\CajaCentral;
use App\Concepto;
use Illuminate\Http\Request;

class CajaCentralController extends Controller
{
    public function index()
    {
        $caja_centrals = CajaCentral::orderBy('created_at', 'desc')->get();
        $saldo_central = number_format(CajaCentral::getSaldo(), 2);
        $saldo_caja = number_format(CajaCentral::getSaldoCaja(), 2);
        $saldo_banco = number_format(CajaCentral::getSaldoBanco(), 2);
        return view("caja_centrals.index", compact("caja_centrals", "saldo_central", "saldo_caja", "saldo_banco"));
    }

    public function create()
    {
        $conceptos = Concepto::all();
        $array_conceptos[""] = "Seleccione...";
        foreach ($conceptos as $value) {
            $array_conceptos[$value->id] = $value->nombre;
        }
        return view("caja_centrals.create", compact("array_conceptos"));
    }

    public function store(Request $request)
    {
        $saldo_caja = CajaCentral::getSaldoCaja();
        $saldo_banco = CajaCentral::getSaldoBanco();
        if ($request->tipo_transaccion == 'CAJA') {
            if ($request->tipo == "EGRESO" && (float)$saldo_caja < (float)$request->monto) {
                return redirect()->route('caja_centrals.create')->with('error_swal', 'No es posible realizar un Egreso, debido a que el saldo actual es de ' . number_format($saldo_caja, 2));
            }
        } else {
            if ($request->tipo == "EGRESO" && (float)$saldo_banco < (float)$request->monto) {
                return redirect()->route('caja_centrals.create')->with('error_swal', 'No es posible realizar un Egreso, debido a que el saldo actual es de ' . number_format($saldo_banco, 2));
            }
        }
        $request["fecha_registro"] = date("Y-m-d");
        CajaCentral::create(array_map("mb_strtoupper", $request->all()));
        return redirect()->route('caja_centrals.index')->with('bien', 'Registro realizado con éxito');
    }

    public function edit(CajaCentral $caja_central)
    {
        $conceptos = Concepto::all();
        $array_conceptos[""] = "Seleccione...";
        foreach ($conceptos as $value) {
            $array_conceptos[$value->id] = $value->nombre;
        }
        return view("caja_centrals.edit", compact("caja_central", "array_conceptos"));
    }
    public function update(CajaCentral $caja_central, Request $request)
    {
        $caja_central->update(array_map("mb_strtoupper", $request->all()));
        return redirect()->route('caja_centrals.index')->with('bien', 'Registro actualizado');
    }

    public function destroy(CajaCentral $caja_central)
    {
        $caja_central->delete();
        return redirect()->route('caja_centrals.index')->with('bien', 'Registro eliminado');
    }
}
