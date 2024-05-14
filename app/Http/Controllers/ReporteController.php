<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\DatosUsuario;
use App\Producto;
use App\KardexProducto;
use App\Caja;
use App\CajaCentral;
use App\Venta;
use App\Cliente;
use App\Concepto;
use App\CuentaCobrar;
use App\CuentaPagar;
use App\DetalleIngreso;
use App\IngresoCaja;
use App\IngresoProducto;
use App\InicioCaja;
use App\Merma;
use App\VentaDetalle;
use App\VentaLote;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReporteController extends Controller
{
    public function index()
    {
        $productos = Producto::where('status', 1)->get();
        $cajas = Caja::where('estado', 1)->get();
        $clientes = Cliente::where('estado', 1)->get();
        $array_productos['todos'] = 'Todos';
        foreach ($productos as $value) {
            $array_productos[$value->id] = $value->nombre;
        }
        $array_cajas['todos'] = "Todos";
        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }
        $array_clientes['todos'] = 'Todos';
        foreach ($clientes as $value) {
            $array_clientes[$value->id] = $value->nombre;
        }

        $fecha_menor = Venta::select("fecha_registro")->orderBy("fecha_registro", "asc")->get()->first();
        $gestiones = [];
        if ($fecha_menor) {
            $gestion_inicial = (int)date("Y", strtotime($fecha_menor->fecha_registro));
            $gestion_actual = (int)date("Y");
            for ($i = $gestion_inicial; $i <= $gestion_actual; $i++) {
                $gestiones[] = $i;
            }
        } else {
            $gestiones = [date("Y")];
        }

        $array_cajas2[''] = "Seleccione...";
        foreach ($cajas as $value) {
            $array_cajas2[$value->id] =  $value->nombre;
        }
        $array_clientes2[''] = 'Seleccione...';
        foreach ($clientes as $value) {
            $array_clientes2[$value->id] = $value->nombre;
        }

        $ingreso_productos = IngresoProducto::orderBy("created_at", "desc")->get();

        return view('reportes.index', compact('array_productos', 'array_cajas', 'array_clientes', 'gestiones', 'array_cajas2', 'array_clientes2', 'ingreso_productos'));
    }

    public function usuarios(Request $request)
    {
        $filtro = $request->filtro;

        $usuarios = DatosUsuario::select('datos_usuarios.*', 'users.id as user_id', 'users.name as usuario', 'users.tipo', 'users.foto')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.status', 1)
            ->orderBy('datos_usuarios.paterno', 'ASC')
            ->get();

        if ($filtro != 'todos') {
            switch ($filtro) {
                case 'tipo':
                    $tipo = $request->tipo;
                    if ($tipo != 'todos') {

                        $usuarios = DatosUsuario::select('datos_usuarios.*', 'users.id as user_id', 'users.name as usuario', 'users.tipo', 'users.foto')
                            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
                            ->where('users.status', 1)
                            ->where('users.tipo', $tipo)
                            ->orderBy('datos_usuarios.paterno', 'ASC')
                            ->get();
                    }
                    break;
            }
        }

        $pdf = PDF::loadView('reportes.usuarios', compact('usuarios'))->setPaper('letter', 'landscape');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Usuarios.pdf');
    }

    public function g_ventas(Request $request)
    {
        $productos = Producto::where('status', 1)->get();
        $cajas = Caja::whereIn('estado', [1, 2])->get();
        $array_productos['todos'] = 'Todos';
        foreach ($productos as $value) {
            $array_productos[$value->id] = $value->nombre;
        }
        $array_cajas['todos'] = "Todos";
        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }
        return view('reportes.grafico_ventas', compact('array_productos', 'array_cajas'));
    }

    public function info_ventas(Request $request)
    {
        $filtro = $request->filtro;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $producto = $request->producto;
        $caja = $request->caja;

        $cajas = Caja::whereIn('estado', [1, 2])->get();

        $productos = Producto::where('status', 1)->get();

        if ($filtro != 'todos') {
            switch ($filtro) {
                case 'caja':
                    if ($caja != 'todos') {
                        $cajas = Caja::whereIn('estado', [1, 2])
                            ->where('id', $caja)->get();
                    }
                    break;
                case 'producto':
                    if ($producto != 'todos') {
                        $productos = Producto::where('status', 1)
                            ->where('id', $producto)->get();
                    }
                    break;
            }
        }

        $categorias = [];
        $contenedor_series = [];
        // OBTENER LAS VENTAS POR CAJA
        // RECORRER LOS PRODUCTOS
        foreach ($cajas as $c) {
            $categorias[] = $c->nombre;
        }

        foreach ($productos as $p) {
            $contenedor_series[$p->id] = [
                'name' => $p->nombre,
                'data' => [],
                'dataLabels' => [
                    'enabled' => true,
                    'rotation' => -90,
                    'color' => '#FFFFFF',
                    'align' => 'right',
                    'format' => '{point.y:.0f}', // one decimal
                    'y' => 10, // 10 pixels down from the top
                    'style' => [
                        'fontSize' => '13px',
                        'fontFamily' => 'Verdana, sans-serif'
                    ]
                ],
            ];
            foreach ($cajas as $c) {
                $cantidad_ventas = count(Venta::select('ventas.id')
                    ->join('venta_detalles', 'venta_detalles.venta_id', '=', 'ventas.id')
                    ->whereIn('ventas.estado', [1, 2])
                    ->where('ventas.caja_id', $c->id)
                    ->where('venta_detalles.producto_id', $p->id)
                    ->get());
                if ($filtro == 'fecha') {
                    $cantidad_ventas = count(Venta::select('ventas.id')
                        ->join('venta_detalles', 'venta_detalles.venta_id', '=', 'ventas.id')
                        ->whereIn('ventas.estado', [1, 2])
                        ->whereBetween('ventas.fecha_venta', [$fecha_ini, $fecha_fin])
                        ->where('ventas.caja_id', $c->id)
                        ->where('venta_detalles.producto_id', $p->id)
                        ->get());
                }
                $contenedor_series[$p->id]['data'][] = (float)$cantidad_ventas;
            }
        }

        $series = [];
        foreach ($contenedor_series as $val) {
            $series[] = $val;
        }

        $fecha = date('d/m/Y');

        return response()->JSON([
            'sw' => true,
            'categorias' => $categorias,
            'series' => $series,
            'fecha' => $fecha
        ]);
    }

    // NUEVOS REPORTES
    public function resultado_operacion(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $total_ventas = 0;
        $total_ventas = Venta::whereIn("estado", [1, 2])
            ->whereBetween("fecha_venta", [$fecha_ini, $fecha_fin])
            ->sum("monto_total");

        $cantidad_compras = IngresoProducto::whereBetween("fecha_ingreso", [$fecha_ini, $fecha_fin])
            ->where("estado", 1)
            ->sum("total_cantidad");
        $total_compras = IngresoProducto::whereBetween("fecha_ingreso", [$fecha_ini, $fecha_fin])
            ->where("estado", 1)
            ->sum("precio_total");

        // INGRESOS
        $id_conceptos = IngresoCaja::where("concepto_id", "!=", 0)
            ->where("tipo_movimiento", "INGRESO")
            ->whereIn("estado", [1, 2])
            ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
            ->distinct()
            ->pluck("concepto_id");
        $conceptos = Concepto::whereIn("id", $id_conceptos)->get();

        $html_ingresos = "";
        $total_ingresos = 0;
        foreach ($conceptos as $concepto) {
            $html_ingresos .= '<h5 class="normal info_monto1  mb-5 mt-5">' . $concepto->nombre;
            $ingreso_cajas = IngresoCaja::where("concepto_id", $concepto->id)
                ->where("tipo_movimiento", "INGRESO")
                ->whereIn("estado", [1, 2])
                ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
                ->sum("monto_total");
            $html_ingresos .= '<span class="bold">' . number_format($ingreso_cajas, 2) . '</span>';
            $html_ingresos .= '</h5>';
            $total_ingresos += (float)$ingreso_cajas;
        }

        // EGRESOS
        $id_conceptos = IngresoCaja::where("concepto_id", "!=", 0)
            ->where("tipo_movimiento", "EGRESO")
            ->whereIn("estado", [1, 2])
            ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
            ->distinct()
            ->pluck("concepto_id");
        $conceptos = Concepto::whereIn("id", $id_conceptos)->get();

        $html_egresos = "";
        $total_egresos = 0;
        foreach ($conceptos as $concepto) {
            $html_egresos .= '<h5 class="normal info_monto1  mb-5 mt-5">' . $concepto->nombre;
            $egreso_cajas = IngresoCaja::where("concepto_id", $concepto->id)
                ->where("tipo_movimiento", "EGRESO")
                ->whereIn("estado", [1, 2])
                ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
                ->sum("monto_total");
            $html_egresos .= '<span class="bold">' . number_format($egreso_cajas, 2) . '</span>';
            $html_egresos .= '</h5>';
            $total_egresos += (float)$egreso_cajas;
        }

        $resultado_ventas = (float)$total_ventas - (float)$total_compras;

        $resultado_neto = (float)$resultado_ventas + $total_ingresos - $total_egresos;

        $pdf = PDF::loadView('reportes.resultado_operacion', compact(
            'fecha_ini',
            'fecha_fin',
            'total_ventas',
            'cantidad_compras',
            'total_compras',
            'html_ingresos',
            'total_ingresos',
            'html_egresos',
            'total_egresos',
            'resultado_ventas',
            'resultado_neto'
        ))->setPaper('letter', 'portrait');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('resultado_operacion.pdf');
    }

    public function indicadores(Request $request)
    {
        $ingreso_producto_id = $request->ingreso_producto_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        if ($ingreso_producto_id != 'todos') {

            $total_cantidad = IngresoProducto::whereBetween("fecha_ingreso", [$fecha_ini, $fecha_fin])
                ->where("estado", 1)
                ->where("id", $ingreso_producto_id)
                ->sum("total_cantidad");
            $total_kilos = IngresoProducto::whereBetween("fecha_ingreso", [$fecha_ini, $fecha_fin])
                ->where("estado", 1)
                ->where("id", $ingreso_producto_id)
                ->sum("total_kilos");
            $ventas_cantidad = VentaLote::join("venta_detalles", "venta_detalles.id", "=", "venta_lotes.venta_detalle_id")
                ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
                ->whereIn("ventas.estado", [1, 2])
                ->where("venta_lotes.ingreso_producto_id", $ingreso_producto_id)
                ->sum("venta_lotes.cantidad");
            $ventas_kilos = VentaLote::join("venta_detalles", "venta_detalles.id", "=", "venta_lotes.venta_detalle_id")
                ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
                ->whereIn("ventas.estado", [1, 2])
                ->where("venta_lotes.ingreso_producto_id", $ingreso_producto_id)
                ->sum("venta_lotes.cantidad_kilos");

            // ARMAR MENUDOS Y BISERAS
            $productos_ids = VentaLote::select("venta_lotes.producto_id")->join("venta_detalles", "venta_detalles.id", "=", "venta_lotes.venta_detalle_id")
                ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                ->whereIn("ventas.estado", [1, 2])
                ->where("venta_lotes.ingreso_producto_id", $ingreso_producto_id)
                ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
                ->distinct()
                ->pluck("venta_lotes.producto_id");

            // armar html para mostrar menudos y biseras
            $html_menudos_biseras = '<table class="table_info">
                                        <tbody>';
            foreach ($productos_ids as $value) {
                $producto = Producto::find($value);
                $cantidad_prod = VentaLote::join("venta_detalles", "venta_detalles.id", "=", "venta_lotes.venta_detalle_id")
                    ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                    ->where("venta_lotes.producto_id", $value)
                    ->whereIn("ventas.estado", [1, 2])
                    ->where("venta_lotes.ingreso_producto_id", $ingreso_producto_id)
                    ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
                    ->sum("venta_lotes.cantidad");
                // html
                $html_menudos_biseras .= '<tr>';
                $html_menudos_biseras .= ' <td width="50%">' . $producto->nombre . '</td>';
                $html_menudos_biseras .= ' <td class="text-right">' . $cantidad_prod . '</td>';

                $res_calculo = 0;
                if ($ventas_cantidad != 0) {
                    $res_calculo = $cantidad_prod / $ventas_cantidad;
                    $res_calculo = round($res_calculo, 2);
                }

                $html_menudos_biseras .= ' <td width="25%" class="bold text-right">' . $res_calculo . '</td>';
                $html_menudos_biseras .= '</tr>';
            }
            $html_menudos_biseras .= '</tbody>
            </table>';
        } else {
            $total_cantidad = IngresoProducto::whereBetween("fecha_ingreso", [$fecha_ini, $fecha_fin])
                ->where("estado", 1)
                ->sum("total_cantidad");
            $total_kilos = IngresoProducto::whereBetween("fecha_ingreso", [$fecha_ini, $fecha_fin])
                ->where("estado", 1)
                ->sum("total_kilos");
            $ventas_cantidad = VentaLote::join("venta_detalles", "venta_detalles.id", "=", "venta_lotes.venta_detalle_id")
                ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
                ->whereIn("ventas.estado", [1, 2])
                ->sum("venta_lotes.cantidad");
            $ventas_kilos = VentaLote::join("venta_detalles", "venta_detalles.id", "=", "venta_lotes.venta_detalle_id")
                ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
                ->whereIn("ventas.estado", [1, 2])
                ->sum("venta_lotes.cantidad_kilos");

            // ARMAR MENUDOS Y BISERAS
            $productos_ids = VentaLote::select("venta_lotes.producto_id")->join("venta_detalles", "venta_detalles.id", "=", "venta_lotes.venta_detalle_id")
                ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                ->whereIn("ventas.estado", [1, 2])
                ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
                ->distinct()
                ->pluck("venta_lotes.producto_id");

            // armar html para mostrar menudos y biseras
            $html_menudos_biseras = '<table class="table_info">
                                        <tbody>';
            foreach ($productos_ids as $value) {
                $producto = Producto::find($value);
                $cantidad_prod = VentaLote::join("venta_detalles", "venta_detalles.id", "=", "venta_lotes.venta_detalle_id")
                    ->join("ventas", "ventas.id", "=", "venta_detalles.venta_id")
                    ->where("venta_lotes.producto_id", $value)
                    ->whereIn("ventas.estado", [1, 2])
                    ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
                    ->sum("venta_lotes.cantidad");
                // html
                $html_menudos_biseras .= '<tr>';
                $html_menudos_biseras .= ' <td width="50%">' . $producto->nombre . '</td>';
                $html_menudos_biseras .= ' <td class="text-right">' . $cantidad_prod . '</td>';

                $res_calculo = 0;
                if ($ventas_cantidad != 0) {
                    $res_calculo = $cantidad_prod / $ventas_cantidad;
                    $res_calculo = round($res_calculo, 2);
                }

                $html_menudos_biseras .= ' <td width="25%" class="bold text-right">' . $res_calculo . '</td>';
                $html_menudos_biseras .= '</tr>';
            }
            $html_menudos_biseras .= '</tbody>
            </table>';
        }

        if ($total_cantidad != 0) {
            $promedio_animales = $total_kilos / $total_cantidad;
        } else {
            $promedio_animales = 0;
        }

        $promedio_animales = round($promedio_animales, 2);

        if ($ventas_cantidad != 0) {
            $promedio_carne = $ventas_kilos / $ventas_cantidad;
        } else {
            $promedio_carne = 0;
        }
        $promedio_carne = round($promedio_carne, 2);
        $promedio_carne_esperado = $promedio_carne * 0.6666;
        $promedio_carne_esperado = round($promedio_carne_esperado, 2);
        $dif_promedio_esperado = $promedio_carne - $promedio_carne_esperado;
        $promedio_bm = $promedio_animales - $promedio_carne;
        // $rendimiento = $ventas_kilos / ($total_kilos * 100);

        // INDICADORES DE INGRESOS Y GASTOS
        $id_conceptos = IngresoCaja::where("concepto_id", "!=", 0)
            ->where("tipo_movimiento", "INGRESO")
            ->whereIn("estado", [1, 2])
            ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
            ->distinct()
            ->pluck("concepto_id");
        $total_ingresos = IngresoCaja::where("tipo_movimiento", "INGRESO")
            ->whereIn("concepto_id", $id_conceptos)
            ->whereIn("estado", [1, 2])
            ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
            ->sum("monto_total");
        $ingreso_operativo =  0;
        if ($ventas_cantidad != 0) {
            $ingreso_operativo = $total_ingresos / $ventas_cantidad;
            $ingreso_operativo = round($ingreso_operativo, 2);
        }


        // GASTOS PERSONAL IDS
        $conceptos_id = Concepto::where("nombre", "LIKE", "%PERSONAL%")
            ->orWhere("nombre", "LIKE", "%FAENEO%")
            ->pluck("id");

        // EGRESOS
        $id_conceptos = IngresoCaja::where("concepto_id", "!=", 0)
            ->where("tipo_movimiento", "EGRESO")
            ->whereIn("estado", [1, 2])
            ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
            ->distinct()
            ->pluck("concepto_id");
        $total_egresos = 0;

        $id_conceptos = $id_conceptos->toArray();
        $conceptos_id = $conceptos_id->toArray();
        $id_conceptos = array_diff($id_conceptos, $conceptos_id);
        $id_conceptos = collect($id_conceptos);
        $egreso_cajas = IngresoCaja::whereIn("concepto_id", $id_conceptos)
            ->where("tipo_movimiento", "EGRESO")
            ->whereIn("estado", [1, 2])
            ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
            ->sum("monto_total");
        Log::debug("BBB");
        $total_egresos += (float)$egreso_cajas;

        $egreso_operativo = 0;
        if ($ventas_cantidad != 0) {
            $egreso_operativo = $total_egresos / $ventas_cantidad;
            $egreso_operativo = round($egreso_operativo, 2);
        }

        $io_eo = $ingreso_operativo - $egreso_operativo;


        // personal
        $gasto_personal = IngresoCaja::whereIn("concepto_id", $conceptos_id)
            ->where("tipo_movimiento", "EGRESO")
            ->whereIn("estado", [1, 2])
            ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
            ->sum("monto_total");

        if ($total_cantidad != 0) {
            $gasto_personal = $gasto_personal / $total_cantidad;
        } else {
            $gasto_personal = 0;
        }

        $pdf = PDF::loadView('reportes.indicadores', compact(
            'fecha_ini',
            'fecha_fin',
            "total_cantidad",
            "total_kilos",
            "promedio_animales",
            "ventas_cantidad",
            "ventas_kilos",
            "promedio_carne",
            "promedio_carne_esperado",
            "dif_promedio_esperado",
            "promedio_bm",
            "html_menudos_biseras",
            "ingreso_operativo",
            "egreso_operativo",
            "io_eo",
            "gasto_personal"
        ))->setPaper('letter', 'portrait');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('indicadores.pdf');
    }

    public function entrega_mb(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $aux_fecha_ini = $fecha_ini;

        $clientes = Cliente::where('estado', 1)->get();

        // armar los días: Lunes, Martes, Miercoles, Jueves, Viernes
        $dias_fechas = [
            '1' => [], //"LUNES",
            '2' => [], //"MARTES",
            '3' => [], //"MIERCOLES",
            '4' => [], //"JUEVES",
            '5' => [], //"VIERNES",
            '6' => [], //"SABADO",
            '0' => [], //"DOMINGO",
        ];

        $array_dias = [
            '1' => 'Lunes',
            '2' => 'Martes',
            '3' => 'Miércoles',
            '4' => 'Jueves',
            '5' => 'Viernes',
            '6' => 'Sábado',
            '0' => 'Domingo',
        ];

        while ($aux_fecha_ini <= $fecha_fin) {
            $dia = ReporteController::getDiaFecha($aux_fecha_ini);
            $dias_fechas[$dia][] = $aux_fecha_ini;
            $aux_fecha_ini = date("Y-m-d", strtotime($aux_fecha_ini . '+1 days'));
        }

        $html_header1 = "";
        $html_header2 = "<tr>";
        $html_header_td_totales = "";
        $html_body = "";
        $totales_fila = [];
        $totales_fila_final = [];
        $totales_finales = [];
        $total_final_bolivianos = 0;
        $productos = Producto::where("status", 1)->get();
        foreach ($dias_fechas as $key => $value) {
            foreach ($productos as $prod) {
                $html_header2 .= '<th>' . $prod->abrev . '</th>';
                if ($key == 1) {
                    $html_header_td_totales .= '<th>' . $prod->abrev . '</th>';
                    $totales_finales["t"][$prod->id] = 0;
                    $totales_fila[$prod->id] = 0;
                }
                $totales_fila_final[$key][$prod->id] = 0;
            }
        }

        $html_header2 .= $html_header_td_totales;
        $html_header2 .= "</tr>";

        foreach ($array_dias as $value) {
            $html_header1 .= '<th colspan="' . count($productos) . '">' . $value . '</th>';
        }
        $html_header1 .= '<th colspan="' . count($productos) . '">TOTALES</th>';
        $html_header1 .= '<th rowspan="2" width="5.3%">BOLIVIANOS</th>';
        $html_totales = '<tr class="totales">';
        $consumo_clientes = [];
        foreach ($clientes as $key_cliente => $cliente) {
            $td_total_cantidades = "";
            $td_total_bolivianos = "";
            $html_body .= "<tr>";
            $html_body .= '<td>' . ((int)$key_cliente + 1) . '</td>';
            $html_body .= '<td>' . $cliente->nombre . '</td>';
            $total_bolivianos = 0;

            $totales_fila = array_fill_keys(array_keys($totales_fila), 0);

            foreach ($dias_fechas as $key => $dia_fecha) {
                foreach ($productos as $prod) {
                    $total_prod_dia = 0;
                    foreach ($dia_fecha as $fecha) {
                        $ventas = DB::select("SELECT SUM(vd.cantidad_kilos) as cantidad FROM venta_detalles vd INNER JOIN ventas v ON v.id = vd.venta_id WHERE v.fecha_venta = '$fecha' AND v.cliente_id = $cliente->id AND vd.producto_id= $prod->id AND v.estado IN (1,2) GROUP BY vd.producto_id");
                        if (isset($ventas[0])) {
                            $total_prod_dia += $ventas[0]->cantidad ? (float)$ventas[0]->cantidad : 0;
                        }
                    }
                    $html_body .= '<td>' . $total_prod_dia . '</td>';
                    $totales_fila[$prod->id] += (float)$total_prod_dia;
                    $totales_fila_final[$key][$prod->id] += (float)$total_prod_dia;
                }
            }

            foreach ($productos as $prod) {
                $td_total_cantidades .= '<td>' . $totales_fila[$prod->id] . '</td>';
                $totales_finales["t"][$prod->id] += (float)$totales_fila[$prod->id];
                $total_bolivianos += (float)$totales_fila[$prod->id] * $prod->precio;
            }

            $total_final_bolivianos += $total_bolivianos;
            $td_total_bolivianos .= '<td class="text-right">' . number_format($total_bolivianos, 2) . '</td>';
            $html_body .= $td_total_cantidades . $td_total_bolivianos . '</tr>';
        }

        $html_totales .= '<td colspan="2">TOTALES</td>';

        foreach ($array_dias as $key => $value) {
            foreach ($productos as $prod) {
                $html_totales .= '<td>' . $totales_fila_final[$key][$prod->id] . '</td>';
            }
        }
        foreach ($productos as $prod) {
            $html_totales .= '<td>' . $totales_finales["t"][$prod->id] . '</td>';
        }
        $html_totales .= '<td class="text-right">' . number_format($total_final_bolivianos, 2) . '</td>';
        $html_totales .= "</tr>";


        // return view('reportes.entrega_mb', compact(
        //     'fecha_ini',
        //     'fecha_fin',
        //     'html_header1',
        //     'html_header2',
        //     'html_body',
        //     'html_totales'
        // ));

        $pdf = PDF::loadView('reportes.entrega_mb', compact(
            'fecha_ini',
            'fecha_fin',
            'html_header1',
            'html_header2',
            'html_body',
            'html_totales'
        ))->setPaper('legal', 'landscape');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('entrega_mb.pdf');
    }


    // FUNCIONES
    public static function getDiaFecha($fecha)
    {
        return date("w", strtotime($fecha));
    }
}
