<?php

namespace App\Http\Controllers;

use App\DetalleIngreso;
use App\Venta;
use App\VentaDetalle;
use App\VentaLote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaLoteController extends Controller
{
    public function venta_lotes()
    {
        DB::beginTransaction();
        try {
            $ventas = Venta::all();

            foreach ($ventas as $venta) {
                $venta_detalles = VentaDetalle::where("venta_id", $venta->id)->get();
                foreach ($venta_detalles as $vd) {
                    $a_detalle_ingreso_id = explode(",", $vd->detalle_ingreso_id);
                    $a_lotes_cantidad = explode(",", $vd->lotes_cantidad);
                    $a_lotes_kilos = explode(",", $vd->lotes_kilos);
                    foreach ($a_detalle_ingreso_id as $key => $item) {
                        // registrar venta lote
                        $detalle_ingreso = DetalleIngreso::find($item);
                        VentaLote::create([
                            "ingreso_producto_id" => $detalle_ingreso->ingreso_producto_id,
                            "detalle_ingreso_id" => $detalle_ingreso->id,
                            "venta_detalle_id" => $vd->id,
                            "producto_id" => $vd->producto_id,
                            "cantidad" => $a_lotes_cantidad[$key],
                            "cantidad_kilos" => $a_lotes_kilos[$key],
                            "precio" => $vd->monto,
                            "fecha" => $vd->venta->fecha_venta
                        ]);
                    }
                }
            }
            DB::commit();
            return 'Registros realizados correctamente. <br/><a href="/">Volver al inicio</a>';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
