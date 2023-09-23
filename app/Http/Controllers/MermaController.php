<?php

namespace App\Http\Controllers;

use App\IngresoProducto;
use App\KardexProducto;
use App\Merma;
use App\Producto;
use Illuminate\Http\Request;

class MermaController extends Controller
{
    public function index()
    {
        $mermas = Merma::orderBy("created_at", "desc")->get();
        return view('mermas.index', compact('mermas'));
    }

    public function create()
    {
        $ingreso_productos = IngresoProducto::where('estado', 1)->get();
        $array_ingreso_productos[""] = "Seleccione...";
        foreach ($ingreso_productos as $value) {
            $array_ingreso_productos[$value->id] = $value->nro_lote;
        }
        $productos = Producto::where('status', 1)
            ->where('estado', 'ACTIVO')
            ->where('status', 1)
            ->get();
        $array_productos[''] = "Seleccione...";
        foreach ($productos as $value) {
            $array_productos[$value->id] =  $value->nombre;
        }
        return view('mermas.create', compact("array_ingreso_productos", 'array_productos'));
    }

    public function store(Request $request)
    {
        $producto = Producto::findOrFail($request->producto_id);
        // $producto = Producto::where("prioridad", "PRINCIPAL")->where("status", 1)->where("estado", "ACTIVO")->get()->first();
        // if (!$producto) {
        //     return redirect()->route('mermas.index')->with('error', 'No se pudo registrar la merma debido a que no hay un producto PRINCIPAL');
        // }
        // if ($producto->stock_actual <= 0) {
        //     return redirect()->route('mermas.index')->with('error', 'No se pudo registrar la merma debido a que el stock del producto PRINCIPAL es de 0');
        // }

        $request["producto_id"] = $producto->id;
        $request["porcentaje"] = ((float)$request->cantidad * 100) / $producto->stock_actual;

        $merma = Merma::create(array_map('mb_strtoupper', $request->all()));
        // stock del PRODUCTO
        $producto->stock_actual = (float)$producto->stock_actual - (float)$merma->cantidad;
        $producto->save();
        // actualizar kardex
        KardexProducto::registroEgreso($producto, $merma->cantidad, 0, "EGRESO DE PRODUCTO POR MERMA");
        return redirect()->route('mermas.index')->with('bien', 'Registro realizado con éxito');
    }

    public function nuevo_merma(Request $request)
    {
        $request['fecha_registro'] = date('Y-m-d');
        $request['estado'] = 1;
        $nuevo_merma = Merma::create(array_map('mb_strtoupper', $request->all()));
        $html = '<option value="' . $nuevo_merma->id . '">' . $nuevo_merma->nombre . '</option>';

        return response()->JSON([
            'sw' => true,
            'html' => $html,
            'i' => $nuevo_merma->id
        ]);
    }

    public function edit(Merma $merma)
    {
        $ingreso_productos = IngresoProducto::where('estado', 1)->get();
        $array_ingreso_productos[""] = "Seleccione...";
        foreach ($ingreso_productos as $value) {
            $array_ingreso_productos[$value->id] = $value->nro_lote;
        }
        return view('mermas.edit', compact('merma', 'array_ingreso_productos'));
    }

    public function update(Merma $merma, Request $request)
    {
        if ($merma->cantidad != $request->cantidad) {
            // REVERTIR MERMA
            // stock del PRODUCTO
            $producto = $merma->producto;
            $producto->stock_actual = (float)$producto->stock_actual + (float)$merma->cantidad;
            $producto->save();
            // actualizar kardex
            KardexProducto::registroSoloIngreso($producto, $merma->cantidad, 0, "INGRESO DE PRODUCTO POR ACTUALIZACIÓN DE REGISTRO MERMA");

            $request["porcentaje"] = ((float)$request->cantidad * 100) / $producto->stock_actual;
            $merma->update(array_map('mb_strtoupper', $request->all()));

            // stock del PRODUCTO
            $producto->stock_actual = (float)$producto->stock_actual - (float)$merma->cantidad;
            $producto->save();
            // actualizar kardex
            KardexProducto::registroEgreso($producto, $merma->cantidad, 0, "EGRESO DE PRODUCTO POR MERMA");
        }

        return redirect()->route('mermas.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Merma $merma)
    {
        return 'mostrar cargo';
    }

    public function destroy(Merma $merma)
    {
        // REVERTIR MERMA
        $detalle_ingreso = $merma->detalle_ingreso;
        $producto = $merma->detalle_ingreso->producto;

        // stock del DETALLE DEL LOTE
        $detalle_ingreso->stock_actual = (float)$detalle_ingreso->stock_actual + (float)$merma->cantidad;
        $detalle_ingreso->save();
        // stock del PRODUCTO
        $producto->stock_actual = (float)$producto->stock_actual + (float)$merma->cantidad;
        $producto->save();
        // actualizar kardex
        KardexProducto::registroSoloIngreso($producto, $merma->cantidad, $detalle_ingreso, "INGRESO DE PRODUCTO POR CORRECIÓN/ELIMINACIÓN DE REGISTRO MERMA");

        $merma->delete();
        return redirect()->route('mermas.index')->with('bien', 'Registro eliminado correctamente');
    }
}
