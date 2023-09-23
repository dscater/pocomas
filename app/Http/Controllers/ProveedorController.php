<?php

namespace App\Http\Controllers;

use App\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public $validate = [
        'propietario' => 'required|min:4',
        'razon_social' => 'required|min:4',
        'fono' => 'required',
        'dir' => 'nullable|min:4',
    ];

    public function index()
    {
        $proveedors = Proveedor::all();
        return view('proveedors.index', compact('proveedors'));
    }

    public function create()
    {
        return view('proveedors.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->validate);
        $request['fecha_registro'] = date('Y-m-d');
        Proveedor::create(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('proveedors.index')->with('bien', 'Registro realizado con éxito');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedors.edit', compact('proveedor'));
    }

    public function update(Proveedor $proveedor, Request $request)
    {
        $request->validate($this->validate);
        $proveedor->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('proveedors.index')->with('bien', 'Registro actualizado');
    }

    public function show(Proveedor $proveedor)
    {
        return view('proveedors.show', compact('proveedor'));
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        return redirect()->route('proveedors.index')->with('bien', 'Registro eliminado');
    }
}
