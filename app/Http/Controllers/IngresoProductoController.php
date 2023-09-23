<?php

namespace App\Http\Controllers;

use App\CajaCentral;
use App\CuentaPagar;
use App\CuentaPagarDetalle;
use App\DetalleIngreso;
use Illuminate\Http\Request;
use App\IngresoProducto;
use App\Producto;
use App\KardexProducto;
use App\Proveedor;
use Illuminate\Support\Facades\DB;

class IngresoProductoController extends Controller
{
    public function index()
    {
        $ingreso_productos = IngresoProducto::where('estado', 1)->get();
        return view('ingreso_productos.index', compact('ingreso_productos'));
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

        return view('ingreso_productos.create', compact('array_productos', 'array_proveedors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            "nro_lote" => "required",
            "proveedor_id" => "required",
            "tipo" => "required",
            "fecha_ingreso" => "required",
        ]);
        DB::beginTransaction();
        try {
            $request['fecha_registro'] = date('Y-m-d');
            $request['estado'] = 1;
            $request['saldo'] = $request->precio_total;
            if ($request->tipo == 'AL CONTADO') {
                $request['saldo'] = 0;
            }
            $nuevo_ingreso = IngresoProducto::create(array_map('mb_strtoupper', $request->except('productos', 'cantidades', 'kilos', 'precios')));

            $productos = $request->productos;
            $cantidades = $request->cantidades;
            $kilos = $request->kilos;
            $precios = $request->precios;

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
            if (isset($productos)) {
                for ($i = 0; $i < count($productos); $i++) {
                    // ACTUALIZAR STOCK DEL PRODUCTO
                    $producto = Producto::find($productos[$i]);
                    $producto->stock_actual = (float)$producto->stock_actual + (float)$kilos[$i];
                    $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad + (float)$cantidades[$i];
                    $producto->save();

                    // REGISTRAR EL INGRESO
                    $detalle_ingreso = DetalleIngreso::create([
                        'ingreso_producto_id'  => $nuevo_ingreso->id,
                        'producto_id'  => $producto->id,
                        'kilos'  => $kilos[$i],
                        'cantidad'  => $cantidades[$i],
                        'stock_kilos'  => $kilos[$i],
                        'stock_cantidad'  => $cantidades[$i],
                        'precio_compra'  => $precios[$i],
                        'anticipo' => 0,
                        'anticipo_kilos' => 0,
                    ]);
                    KardexProducto::registroIngreso($producto, $nuevo_ingreso, $detalle_ingreso);
                }
            }
            DB::commit();
            return redirect()->route('ingreso_productos.index')->with('bien', 'Registro realizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ingreso_productos.index')->with('error', 'Ocurrió un error. El registro no se guardo. ' . $e->getMessage());
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
        return view('ingreso_productos.edit', compact('ingreso_producto', 'array_productos', 'array_proveedors'));
    }

    public function update(IngresoProducto $ingreso_producto, Request $request)
    {
        DB::beginTransaction();
        try {
            $request['saldo'] = $request->precio_total;
            if ($request->tipo == 'AL CONTADO') {
                $request['saldo'] = 0;
            }
            $ingreso_producto->update(array_map('mb_strtoupper', $request->except('productos', 'cantidades', 'kilos', 'control_stock', 'precios', 'eliminados')));

            $eliminados = $request->eliminados;
            if (isset($eliminados)) {
                for ($i = 0; $i < count($eliminados); $i++) {
                    $detalle_ingreso = DetalleIngreso::find($eliminados[$i]);
                    //restar la cantidad del ingreso eliminado
                    $producto = $detalle_ingreso->producto;
                    $producto->stock_actual = (float)$producto->stock_actual - (float)$detalle_ingreso->kilos;
                    $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad - (float)$detalle_ingreso->cantidad;
                    $producto->save();
                    // quitar del kardex
                    $kardex = KardexProducto::where('detalle_ingreso_id', $detalle_ingreso->id)->get()->first();
                    $kardex->delete();
                    $detalle_ingreso->delete();
                }
            }

            $productos = $request->productos;
            $cantidades = $request->cantidades;
            $kilos = $request->kilos;
            $precios = $request->precios;
            $control_stock = $request->control_stock;

            if (isset($productos)) {
                for ($i = 0; $i < count($productos); $i++) {
                    // ACTUALIZAR STOCK DEL PRODUCTO
                    $producto = Producto::find($productos[$i]);
                    $producto->stock_actual = (float)$producto->stock_actual + (float)$kilos[$i];
                    $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad + (float)$cantidades[$i];
                    $producto->save();

                    // REGISTRAR EL INGRESO
                    $detalle_ingreso = DetalleIngreso::create([
                        'ingreso_producto_id'  => $ingreso_producto->id,
                        'producto_id'  => $producto->id,
                        'kilos'  => $kilos[$i],
                        'cantidad'  => $cantidades[$i],
                        'tipo_control'  => $control_stock[$i],
                        'stock_kilos'  => $kilos[$i],
                        'stock_cantidad'  => $cantidades[$i],
                        'precio_compra'  => $precios[$i],
                        'anticipo' => 0,
                        'anticipo_kilos' => 0,
                    ]);
                    KardexProducto::registroIngreso($producto, $ingreso_producto, $detalle_ingreso);
                }
            }
            DB::commit();
            return redirect()->route('ingreso_productos.index')->with('bien', 'Registro modificado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ingreso_productos.index')->with('error', 'Error al actualizar. El registro no se guardo. ' . $e->getMessage());
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
            foreach ($ingreso_producto->detalle_ingresos as $di) {
                $cantidad_anterior_kilos = (float)$di->stock_kilos;
                $cantidad_anterior = (float)$di->stock_cantidad;
                $producto = $di->producto;
                $producto->stock_actual = (float)$producto->stock_actual - (float)$cantidad_anterior_kilos;
                $producto->stock_actual_cantidad = (float)$producto->stock_actual_cantidad - (float)$cantidad_anterior;
                $producto->save();
            }
            $ingreso_producto->estado = 0;
            $ingreso_producto->save();
            DB::commit();
            return redirect()->route('ingreso_productos.index')->with('bien', 'Registro eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ingreso_productos.index')->with('error', 'Error al eliminar. ' . $e->getMessage());
        }
    }

    public function getIngreso(Request $request)
    {
        $proveedor_id = $request->proveedor_id;

        $ingreso_productos = IngresoProducto::where("proveedor_id", $proveedor_id)->where("tipo", "POR PAGAR")->where("saldo", ">", 0)->orderBy("created_at", "asc")->get();

        $html = view("cuenta_pagars.parcial.lista_cuentas", compact("ingreso_productos"))->render();
        return response()->JSON([
            "html" => $html,
            "total_cuentas" => count($ingreso_productos)
        ]);
    }

    public function getProductosLote(Request $request)
    {
        $html = '<option value="">- Sin Productos -</option>';
        if (isset($request->stock)) {
            $html = '<option value="">Seleccione...</option>';
            $detalle_ingresos = DetalleIngreso::where("ingreso_producto_id", $request->id)->where("stock_kilos", ">", 0)
                ->where("stock_cantidad", ">", 0)->get();
            foreach ($detalle_ingresos as $value) {
                $html .= '<option value="' . $value->id . '">' . $value->producto->nombre . '</option>';
            }
        } else {
            $html = '<option value="">Seleccione...</option>';
            $detalle_ingresos = DetalleIngreso::where("ingreso_producto_id", $request->id)->get();
            foreach ($detalle_ingresos as $value) {
                $html .= '<option value="' . $value->id . '">' . $value->producto->nombre . '</option>';
            }
        }
        return response()->JSON($html);
    }
}
