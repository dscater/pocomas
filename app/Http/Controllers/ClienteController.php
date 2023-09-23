<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cliente;
use App\CuentaCobrar;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::where('estado', 1)->get();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request['fecha_registro'] = date('Y-m-d');
        $request['estado'] = 1;
        Cliente::create(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('clientes.index')->with('bien', 'Registro realizado con éxito');
    }

    public function nuevo_cliente(Request $request)
    {
        $request['fecha_registro'] = date('Y-m-d');
        $request['estado'] = 1;
        $nuevo_cliente = Cliente::create(array_map('mb_strtoupper', $request->all()));
        $html = '<option value="' . $nuevo_cliente->id . '">' . $nuevo_cliente->nombre . '</option>';

        return response()->JSON([
            'sw' => true,
            'html' => $html,
            'i' => $nuevo_cliente->id,
            "cliente" => $nuevo_cliente
        ]);
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Cliente $cliente, Request $request)
    {
        $cliente->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('clientes.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Cliente $cliente)
    {
        return 'mostrar cargo';
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->estado = 0;
        $cliente->save();
        return redirect()->route('clientes.index')->with('bien', 'Registro eliminado correctamente');
    }

    public function getInfoCliente(Request $request)
    {
        if ($request->tipo == 'select') {
            $cliente = Cliente::find($request->cliente_id);
        } else {
            $cliente = Cliente::where('ci', $request->ci)->get()->first();
        }
        if ($cliente) {
            return response()->JSON([
                'sw' => true,
                'cliente' => $cliente
            ]);
        } else {
            return response()->JSON([
                'sw' => false,
                'cliente' => null
            ]);
        }
    }

    public function cuentas_cobrar(Request $request)
    {
        $cliente = Cliente::where('id', $request->cliente_id)->get()->first();
        $cuentas = CuentaCobrar::where('cliente_id', $cliente->id)
            ->where('estado', 'PENDIENTE')
            ->where('status', 1)
            ->orderBy('created_at', 'asc')->get();
        $html = view("cuenta_cobrars.parcial.lista_cuentas", compact("cuentas"))->render();

        return response()->JSON([
            'sw' => true,
            'html' => $html,
            "total_cuentas" => count($cuentas)
        ]);
    }
}
