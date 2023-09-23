<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Caja;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::where('estado', 1)->get();
        return view('cajas.index', compact('cajas'));
    }

    public function create()
    {
        return view('cajas.create');
    }

    public function store(Request $request)
    {
        $request['fecha_registro'] = date('Y-m-d');
        $request['estado'] = 1;
        Caja::create(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('cajas.index')->with('bien', 'Registro realizado con éxito');
    }

    public function edit(Caja $caja)
    {
        return view('cajas.edit', compact('caja'));
    }

    public function update(Caja $caja, Request $request)
    {
        $caja->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('cajas.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Caja $caja)
    {
        return 'mostrar cargo';
    }

    public function destroy(Caja $caja)
    {
        $caja->estado = 0;
        $caja->save();
        return redirect()->route('cajas.index')->with('bien', 'Registro eliminado correctamente');
    }
}
