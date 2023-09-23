<?php

namespace App\Http\Controllers;

use App\DetalleIngreso;
use App\IngresoProducto;
use App\KardexProducto;
use App\Merma;
use App\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MermaController extends Controller
{
    public function index()
    {
        $mermas = Merma::orderBy("created_at", "desc")->get();
        return view('mermas.index', compact('mermas'));
    }

    public function create()
    {
        $lotes = IngresoProducto::where('estado', 1)
            ->get();
        $array_lotes[''] = "Seleccione...";
        foreach ($lotes as $value) {
            $array_lotes[$value->id] =  $value->nro_lote;
        }
        return view('mermas.create', compact('array_lotes'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $detalle_ingreso = DetalleIngreso::findOrFail($request->detalle_ingreso_id);
            $request["detalle_ingreso_id"] = $detalle_ingreso->id;
            $request["porcentaje"] = ((float)$request->cantidad_kilos * 100) / $detalle_ingreso->stock_kilos;

            $merma = Merma::create(array_map('mb_strtoupper', $request->all()));
            // stock del detalle
            $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos - (float)$merma->cantidad_kilos;
            $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad - (float)$merma->cantidad;
            $detalle_ingreso->save();
            // stock del producto
            $producto = $detalle_ingreso->producto;
            $producto->stock_actual = (float)$producto->stock_actual - (float)$merma->cantidad_kilos;
            $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad - (float)$merma->cantidad;
            $producto->save();
            // actualizar kardex
            KardexProducto::registroEgreso($producto, $merma->cantidad_kilos, 0, "EGRESO DE PRODUCTO POR MERMA");
            DB::commit();
            return redirect()->route('mermas.index')->with('bien', 'Registro realizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('mermas.index')->with('error', 'Ocurrió un error inesperado. ' . $e->getMessage());
        }
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
        $lotes = IngresoProducto::where('estado', 1)
            ->get();
        $array_lotes[''] = "Seleccione...";
        foreach ($lotes as $value) {
            $array_lotes[$value->id] =  $value->nro_lote;
        }
        return view('mermas.edit', compact('merma', 'array_lotes'));
    }

    public function update(Merma $merma, Request $request)
    {
        if ($merma->cantidad != $request->cantidad || $merma->cantidad_kilos != $request->cantidad_kilos || $merma->detalle_ingreso_id != $request->detalle_ingreso_id) {
            DB::beginTransaction();
            try {
                // REVERTIR MERMA
                // stock del PRODUCTO
                $producto = $merma->detalle_ingreso->producto;
                $producto->stock_actual = (float)$producto->stock_actual + (float)$merma->cantidad_kilos;
                $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad + (float)$merma->cantidad;
                $producto->save();
                // stock del detalle ingreso
                $detalle_ingreso = $merma->detalle_ingreso;
                $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos + (float)$merma->cantidad_kilos;
                $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad + (float)$merma->cantidad;
                $detalle_ingreso->save();
                // actualizar kardex
                KardexProducto::registroSoloIngreso($producto, $merma->cantidad_kilos, $detalle_ingreso->id, "INGRESO DE PRODUCTO POR ACTUALIZACIÓN DE REGISTRO MERMA");

                $request["porcentaje"] = ((float)$request->cantidad_kilos * 100) / $producto->stock_actual;
                $merma->update(array_map('mb_strtoupper', $request->all()));

                $merma_actualizado = Merma::find($merma->id);
                // stock del detalle ingreso
                $detalle_ingreso = $merma_actualizado->detalle_ingreso;
                $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos - (float)$merma->cantidad_kilos;
                $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad - (float)$merma->cantidad;
                $detalle_ingreso->save();

                // stock del PRODUCTO
                $producto = $merma_actualizado->detalle_ingreso->producto;
                $producto->stock_actual = (float)$producto->stock_actual - (float)$merma->cantidad_kilos;
                $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad - (float)$merma->cantidad;
                $producto->save();
                // actualizar kardex
                KardexProducto::registroEgreso($producto, $merma->cantidad, $detalle_ingreso->id, "EGRESO DE PRODUCTO POR MERMA");
                DB::commit();
                return redirect()->route('mermas.index')->with('bien', 'Registro modificado con éxito');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('mermas.index')->with('error', 'Ocurrió un error inesperado. ' . $e->getMessage());
            }
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
