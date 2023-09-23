<?php

namespace App\Http\Controllers;

use App\DetalleIngreso;
use App\IngresoProducto;
use Illuminate\Http\Request;
use App\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::where('status', 1)->get();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $prioridad = $request->prioridad;
        if ($prioridad == 'PRINCIPAL') {
            $existe = Producto::where("prioridad", "PRINCIPAL")->where("status", 1)->get()->first();
            if ($existe) {
                return redirect()->route('productos.index')->with('error', 'No se pudo realizar el registro, debido a que ya existe un Producto con prioridad PRINCIPAL');
            }
        }

        $request['fecha_registro'] = date('Y-m-d');
        $request['stock_actual'] = 0;
        $request['stock_actual_cantidad'] = 0;
        $request['status'] = 1;
        $request['foto'] = '';
        $nuevo_producto = new Producto(array_map('mb_strtoupper', $request->except('foto')));
        $nuevo_producto->foto = 'producto_default.png';
        if ($request->hasFile('foto')) {
            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = \str_replace(' ', '_', $nuevo_producto->nombre) . time() . $extension;
            $file_foto->move(public_path() . "/imgs/productos/", $nom_foto);
            $nuevo_producto->foto = $nom_foto;
        }
        $nuevo_producto->save();

        return redirect()->route('productos.index')->with('bien', 'Registro realizado con éxito');
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Producto $producto, Request $request)
    {
        $prioridad = $request->prioridad;
        if ($prioridad == 'PRINCIPAL') {
            $existe = Producto::where("prioridad", "PRINCIPAL")->where("status", 1)->where("id", "!=", $producto->id)->get()->first();
            if ($existe) {
                return redirect()->route('productos.index')->with('error', 'No se pudo actualizar el registro, debido a que ya existe un Producto con prioridad PRINCIPAL');
            }
        }

        $producto->update(array_map('mb_strtoupper', $request->except('foto')));
        if ($request->hasFile('foto')) {
            // antiguo
            $antiguo = $producto->foto;
            if ($antiguo != 'producto_default.png') {
                \File::delete(public_path() . '/imgs/productos/' . $antiguo);
            }

            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = \str_replace(' ', '_', $producto->nombre) . time() . $extension;
            $file_foto->move(public_path() . "/imgs/productos/", $nom_foto);
            $producto->foto = $nom_foto;
            $producto->save();
        }
        return redirect()->route('productos.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Producto $producto)
    {
        return 'mostrar cargo';
    }

    public function destroy(Producto $producto)
    {
        $producto->status = 0;
        $producto->save();
        return redirect()->route('productos.index')->with('bien', 'Registro eliminado correctamente');
    }

    public function getInfoVenta(Request $request)
    {
        $producto_venta = DetalleIngreso::find($request->detalle_ingreso_id);
        $producto_info = DetalleIngreso::find($request->detalle_ingreso_id);
        // stock disponible
        $stock_kilos = $producto_info->stock_kilos;
        $stock_cantidad = $producto_info->stock_cantidad;

        // valores soliticados
        $cantidad = $request->cantidad;
        $cantidad_kilos = $request->cantidad_kilos;

        if ($stock_kilos >= $cantidad_kilos && $stock_cantidad > $cantidad) {

            // $producto_info->stock_kilos -= (float)$cantidad_kilos;
            // $producto_info->stock_cantidad -= (float)$stock_cantidad;
            // $producto_info->save();

            return response()->JSON([
                'sw' => true,
                'detalle_ingreso' => $producto_info,
                'producto' => $producto_venta->producto,
                'precio' => $producto_venta->producto->precio
            ]);
        } else {
            return response()->JSON([
                'sw' => false,
                'msg' => 'El stock actual en el lote del producto que seleccionó es de:<br> <p class="text-center mb-1"><strong>' . $stock_kilos . ' kilos</strong> | <strong>' . $stock_cantidad . ' cerdos</strong></p>Insuficiente para las cantidades que seleccionó',
            ]);
        }
    }

    public function getMedida(Request $request)
    {
        $producto = Producto::find($request->producto_id);
        if ($producto->medida == 'UNIDAD') {
            $medida = 'Cantidad Unidad*';
        } else {
            $medida = 'Cantidad Kilos*';
        }
        return response()->JSON([
            'sw' => false,
            'medida' => $medida
        ]);
    }
}
