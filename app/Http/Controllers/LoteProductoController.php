<?php

namespace App\Http\Controllers;

use App\CuentaPagar;
use App\DetalleIngreso;
use App\IngresoProducto;
use App\KardexProducto;
use App\Producto;
use App\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoteProductoController extends Controller
{
    public function index()
    {
        $ingreso_productos = IngresoProducto::where('estado', 1)->get();
        return view('lote_productos.index', compact('ingreso_productos'));
    }

    public function create()
    {
        $productos = Producto::where('status', 1)
            ->where('estado', 'ACTIVO')
            ->where('status', 1)
            ->get();
        $proveedors = Proveedor::orderBy('razon_social', 'asc')->get();
        $array_proveedors[''] = "Seleccione...";
        foreach ($proveedors as $value) {
            $array_proveedors[$value->id] = "$value->razon_social - $value->propietario";
        }

        $array_productos[''] = "Seleccione...";
        foreach ($productos as $value) {
            $array_productos[$value->id] =  $value->nombre;
        }

        return view('lote_productos.create', compact('array_productos', 'array_proveedors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            "nro_lote" => "required",
            "proveedor_id" => "required",
            "producto_id" => "required",
            "precio_compra" => "required|numeric|min:1",
            "total_kilos" => "required|numeric|min:1",
            "total_cantidad" => "required|numeric|min:1",
            "tipo" => "required",
            "fecha_ingreso" => "required",
        ]);
        DB::beginTransaction();
        try {
            $request['fecha_registro'] = date('Y-m-d');
            $request['estado'] = 1;
            // kilos
            $request['saldo_kilos'] = $request->total_kilos;
            $request['saldo_cantidad'] = $request->total_cantidad;
            // montos
            $request['precio_total'] = $request->precio_compra;
            $request['saldo'] = $request->precio_total;
            if ($request->tipo == 'AL CONTADO') {
                $request['saldo'] = 0;
            }
            $nuevo_ingreso = IngresoProducto::create(array_map('mb_strtoupper', $request->all()));
            // REGISTRAR CUENTA POR PAGAR
            if ($request->tipo == 'POR PAGAR') {
                CuentaPagar::create([
                    "ingreso_producto_id" => $nuevo_ingreso->id,
                    "proveedor_id" => $nuevo_ingreso->proveedor_id,
                    "monto_total" => $request->precio_total,
                    "saldo" => $request->precio_total,
                    "fecha_registro" => date("Y-m-d"),
                ]);
            }

            // ACTUALIZAR STOCK PRODUCTO
            $producto = Producto::find($nuevo_ingreso->producto_id);
            $producto->stock_actual = (float)$producto->stock_actual + (float)$nuevo_ingreso->total_kilos;
            $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad + (float)$nuevo_ingreso->total_cantidad;
            $producto->save();
            // ACTUALIZAR KARDEX
            KardexProducto::registroIngresoLote($producto, $nuevo_ingreso);

            // REGISTRAR EL INGRESO DEL DETALLE
            DetalleIngreso::create([
                'ingreso_producto_id'  => $nuevo_ingreso->id,
                'producto_id'  => $producto->id,
                'kilos'  => $nuevo_ingreso->total_kilos,
                'cantidad'  => $nuevo_ingreso->total_cantidad,
                'stock_kilos'  => $nuevo_ingreso->total_kilos,
                'stock_cantidad'  => $nuevo_ingreso->total_cantidad,
                'anticipo' => 0,
                'anticipo_kilos' => 0,
            ]);

            DB::commit();
            return redirect()->route('lote_productos.index')->with('bien', 'Registro realizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('lote_productos.index')->with('error', 'Ocurrió un error. El registro no se guardo. ' . $e->getMessage());
        }
    }

    public function edit(IngresoProducto $ingreso_producto)
    {
        $productos = Producto::where('status', 1)
            ->where('estado', 'ACTIVO')
            ->where('status', 1)
            ->get();

        $proveedors = Proveedor::orderBy('razon_social', 'asc')->get();
        $array_proveedors[''] = "Seleccione...";
        foreach ($proveedors as $value) {
            $array_proveedors[$value->id] = "$value->razon_social - $value->propietario";
        }

        $array_productos[''] = "Seleccione...";
        foreach ($productos as $value) {
            $array_productos[$value->id] =  $value->nombre;
        }
        return view('lote_productos.edit', compact('ingreso_producto', 'array_productos', 'array_proveedors'));
    }

    public function update(IngresoProducto $ingreso_producto, Request $request)
    {
        DB::beginTransaction();
        try {
            $detalle_ingreso_inicial = DetalleIngreso::where("producto_id", $ingreso_producto->producto_id)->get()->first();
            if ($ingreso_producto->total_kilos != $request->total_kilos || $ingreso_producto->total_cantidad != $request->total_cantidad || $ingreso_producto->producto_id != $request->producto_id) {
                // reestablecer stock del producto
                $producto = Producto::find($ingreso_producto->producto_id);
                $producto->stock_actual = (float)$producto->stock_actual - (float)$ingreso_producto->total_kilos;
                $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad - (float)$ingreso_producto->total_cantidad;
                $producto->save();
                // ACTUALIZAR KARDEX
                KardexProducto::registroEgreso($producto, $ingreso_producto->total_kilos, $ingreso_producto->id, "EGRESO POR ACTUALIZACIÓN DE REGISTRO", "IngresoProducto");

                // kilos
                $request['saldo_kilos'] = $request->total_kilos;
                $request['saldo_cantidad'] = $request->total_cantidad;
            }

            // montos
            $request['precio_total'] = $request->precio_compra;
            $request['saldo'] = $request->precio_total;
            if ($request->tipo == 'AL CONTADO') {
                $request['saldo'] = 0;
            }

            $ingreso_producto->update(array_map('mb_strtoupper', $request->all()));

            // ACTUALIZAR STOCK PRODUCTO
            $producto = Producto::find($ingreso_producto->producto_id);
            $producto->stock_actual = (float)$producto->stock_actual + (float)$ingreso_producto->total_kilos;
            $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad + (float)$ingreso_producto->total_cantidad;
            $producto->save();
            // ACTUALIZAR KARDEX
            KardexProducto::registroIngresoLote($producto, $ingreso_producto);

            // REGISTRAR EL INGRESO DEL DETALLE
            $detalle_ingreso_inicial->update([
                'ingreso_producto_id'  => $ingreso_producto->id,
                'producto_id'  => $ingreso_producto->producto_id,
                'kilos'  => $ingreso_producto->total_kilos,
                'cantidad'  => $ingreso_producto->total_cantidad,
                'stock_kilos'  => $ingreso_producto->total_kilos,
                'stock_cantidad'  => $ingreso_producto->total_cantidad,
                'anticipo' => 0,
                'anticipo_kilos' => 0,
            ]);

            DB::commit();
            return redirect()->route('lote_productos.index')->with('bien', 'Registro modificado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('lote_productos.index')->with('error', 'Error al actualizar. El registro no se guardo. ' . $e->getMessage());
        }
    }

    public function show(IngresoProducto $ingreso_producto)
    {
        return 'mostrar cargo';
    }

    public function destroy(IngresoProducto $ingreso_producto)
    {
        DB::beginTransaction();
        try {
            if ($ingreso_producto->existe_ventas) {
                throw new Exception("No es posible eliminar el registro debido a que existen ventas que utilizaron productos del lote: " . $ingreso_producto->nro_lote);
            }


            if ($ingreso_producto->existe_pagos) {
                throw new Exception("No es posible eliminar el registro debido a que existen pagos realizados del lote: " . $ingreso_producto->nro_lote);
            }


            // eliminar cuenta pagar
            $cuenta_pagar = $ingreso_producto->cuenta_pagars;
            $cuenta_pagar->cuenta_pagar_detalles()->delete();
            $cuenta_pagar->delete();

            foreach ($ingreso_producto->detalle_ingresos as $di) {
                $cantidad_anterior_kilos = (float)$di->stock_kilos;
                $cantidad_anterior = (float)$di->stock_cantidad;
                $producto = $di->producto;
                $producto->stock_actual = (float)$producto->stock_actual - (float)$cantidad_anterior_kilos;
                $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad - (float)$cantidad_anterior;
                $producto->save();

                KardexProducto::registroEgreso($producto, (float)$cantidad_anterior_kilos, $di->id, "EGRESO POR ELIMINACIÓN DEL LOTE N° " . $ingreso_producto->nro_lote, "IngresoProducto");
            }
            $ingreso_producto->estado = 0;
            $ingreso_producto->save();
            DB::commit();
            return redirect()->route('lote_productos.index')->with('bien', 'Registro eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('lote_productos.index')->with('error', 'Error al eliminar. ' . $e->getMessage());
        }
    }

    public function getInfoParaRegistroIngreso(Request $request)
    {
        $ingreso_producto = IngresoProducto::find($request->ingreso_producto_id);
        $productos = Producto::where('status', 1)->where("id", "!=", $ingreso_producto->producto_id)->get();
        $principal = Producto::find($ingreso_producto->producto_id);

        $options = '<option value="">- Seleccione -</option>';
        foreach ($productos as $p) {
            $options .= '<option value="' . $p->id . '">' . $p->nombre . '</option>';
        }

        $detalle_ingresos = DetalleIngreso::where("ingreso_producto_id", $ingreso_producto->id)
            ->where("producto_id", "!=", $ingreso_producto->producto_id)
            ->get();
        $html = "";
        if (count($detalle_ingresos) > 0) {
            $html = view("ingreso_productos.parcial.productos", compact("detalle_ingresos"))->render();
        }

        return response()->JSON([
            "ingreso_producto" => $ingreso_producto,
            "productos" => $productos,
            "principal" => $principal,
            "options" => $options,
            "html" => $html
        ]);
    }
}
