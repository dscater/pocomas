<?php

namespace App\Http\Controllers;

use App\CajaCentral;
use App\Concepto;
use App\IngresoCaja;
use Illuminate\Http\Request;

class ConceptoController extends Controller
{
    public function index()
    {
        $conceptos = Concepto::all();
        return view('conceptos.index', compact('conceptos'));
    }

    public function create()
    {
        return view('conceptos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            "nombre" => "required|unique:conceptos,nombre"
        ]);
        Concepto::create(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('conceptos.index')->with('bien', 'Registro realizado con éxito');
    }

    public function edit(Concepto $concepto)
    {
        return view('conceptos.edit', compact('concepto'));
    }

    public function update(Concepto $concepto, Request $request)
    {
        $request->validate([
            "nombre" => "required|unique:conceptos,nombre," . $concepto->id
        ]);
        $concepto->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('conceptos.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Concepto $concepto)
    {
        return 'mostrar cargo';
    }

    public function destroy(Concepto $concepto)
    {
        $uso = CajaCentral::where("concepto_id", $concepto->id)->get();
        if (count($uso) > 0) {
            return redirect()->route('conceptos.index')->with('error', 'No es posible eliminar el registro porque esta siendo utilizado');
        }
        $uso = IngresoCaja::where("concepto_id", $concepto->id)->get();
        if (count($uso) > 0) {
            return redirect()->route('conceptos.index')->with('error', 'No es posible eliminar el registro porque esta siendo utilizado');
        }
        $concepto->delete();
        return redirect()->route('conceptos.index')->with('bien', 'Registro eliminado correctamente');
    }
}
