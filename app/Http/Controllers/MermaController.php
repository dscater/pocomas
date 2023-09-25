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
            $producto = Producto::find($request->producto_id);
            $cantidad_kilos = $request->cantidad_kilos;
            $cantidad = $request->cantidad;

            $detalle_ingresos = DetalleIngreso::where("ingreso_producto_id", $request->ingreso_producto_id)
                ->where("producto_id", $request->producto_id)
                ->where("stock_kilos", ">", 0)
                ->where("stock_cantidad", ">", 0)
                ->get();

            $request["producto_id"] = $producto->id;
            $request["porcentaje"] = ((float)$cantidad_kilos * 100) / $producto->stock_actual;

            $merma = Merma::create(array_map('mb_strtoupper', $request->all()));

            // actualizar stock lotes
            $cantidad_kilos_restante = $cantidad_kilos;
            $cantidad_restante = $cantidad;
            foreach ($detalle_ingresos as $di) {
                if ($cantidad_kilos_restante == 0 && $cantidad_restante == 0) {
                    break;
                }
                // cantidad kilos
                if ($cantidad_kilos_restante > 0) {
                    if ($di->stock_kilos >= $cantidad_kilos_restante) {
                        $di->stock_kilos = $di->stock_kilos - $cantidad_kilos_restante;
                        $cantidad_kilos_restante = 0;
                    } else {
                        $cantidad_kilos_restante = $cantidad_kilos_restante - $di->stock_kilos;
                        $di->stock_kilos = 0;
                    }
                }
                // cantidad cerdos
                if ($cantidad_restante > 0) {
                    if ($di->stock_cantidad >= $cantidad_restante) {
                        $di->stock_cantidad = $di->stock_cantidad - $cantidad_restante;
                        $cantidad_restante = 0;
                    } else {
                        $cantidad_restante = $cantidad_restante - $di->stock_cantidad;
                        $di->stock_cantidad = 0;
                    }
                }
                $di->save();
            }
            // stock del producto
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
        if ($merma->cantidad != $request->cantidad || $merma->cantidad_kilos != $request->cantidad_kilos || $merma->producto_id != $request->producto_id) {
            DB::beginTransaction();
            try {
                // REVERTIR MERMA
                // stock del PRODUCTO
                $producto = $merma->producto;
                $producto->stock_actual = (float)$producto->stock_actual + (float)$merma->cantidad_kilos;
                $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad + (float)$merma->cantidad;
                $producto->save();
                // stock del detalle ingreso
                $detalle_ingreso = DetalleIngreso::where("ingreso_producto_id", $merma->ingreso_producto_id)
                    ->where("producto_id", $request->producto_id)
                    ->get()->last();
                $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos + (float)$merma->cantidad_kilos;
                $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad + (float)$merma->cantidad;
                $detalle_ingreso->save();
                // actualizar kardex
                KardexProducto::registroSoloIngreso($producto, $merma->cantidad_kilos, $detalle_ingreso->id, "INGRESO DE PRODUCTO POR ACTUALIZACIÓN DE REGISTRO MERMA");

                $request["porcentaje"] = ((float)$request->cantidad_kilos * 100) / $producto->stock_actual;
                $merma->update(array_map('mb_strtoupper', $request->all()));

                $detalle_ingresos = DetalleIngreso::where("ingreso_producto_id", $request->ingreso_producto_id)
                    ->where("producto_id", $request->producto_id)
                    ->where("stock_kilos", ">", 0)
                    ->where("stock_cantidad", ">", 0)
                    ->get();

                $merma_actualizado = Merma::find($merma->id);
                // stock del detalle ingreso
                // actualizar stock lotes
                $cantidad_kilos_restante = $merma_actualizado->cantidad_kilos;
                $cantidad_restante = $merma_actualizado->cantidad;
                foreach ($detalle_ingresos as $di) {
                    if ($cantidad_kilos_restante == 0 && $cantidad_restante == 0) {
                        break;
                    }
                    // cantidad kilos
                    if ($cantidad_kilos_restante > 0) {
                        if ($di->stock_kilos >= $cantidad_kilos_restante) {
                            $di->stock_kilos = $di->stock_kilos - $cantidad_kilos_restante;
                            $cantidad_kilos_restante = 0;
                        } else {
                            $cantidad_kilos_restante = $cantidad_kilos_restante - $di->stock_kilos;
                            $di->stock_kilos = 0;
                        }
                    }
                    // cantidad cerdos
                    if ($cantidad_restante > 0) {
                        if ($di->stock_cantidad >= $cantidad_restante) {
                            $di->stock_cantidad = $di->stock_cantidad - $cantidad_restante;
                            $cantidad_restante = 0;
                        } else {
                            $cantidad_restante = $cantidad_restante - $di->stock_cantidad;
                            $di->stock_cantidad = 0;
                        }
                    }
                    $di->save();
                }
                // stock del PRODUCTO
                $producto = $merma_actualizado->producto;
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
        $producto = $merma->producto;

        // stock del DETALLE DEL LOTE
        $detalle_ingreso = DetalleIngreso::where("ingreso_producto_id", $merma->ingreso_producto_id)
            ->where("producto_id", $merma->producto_id)
            ->get()->last();
        $detalle_ingreso->stock_kilos = (float)$detalle_ingreso->stock_kilos + (float)$merma->cantidad_kilos;
        $detalle_ingreso->stock_cantidad = (float)$detalle_ingreso->stock_cantidad + (float)$merma->cantidad;
        $detalle_ingreso->save();
        // stock del PRODUCTO
        $producto->stock_actual = (float)$producto->stock_actual + (float)$merma->cantidad_kilos;
        $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad + (float)$merma->cantidad;
        $producto->save();
        // actualizar kardex
        KardexProducto::registroSoloIngreso($producto, $merma->cantidad_kilos, 0, "INGRESO DE PRODUCTO POR CORRECIÓN/ELIMINACIÓN DE REGISTRO MERMA");

        $merma->delete();
        return redirect()->route('mermas.index')->with('bien', 'Registro eliminado correctamente');
    }
}
