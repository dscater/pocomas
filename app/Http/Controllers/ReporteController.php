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
use App\IngresoCaja;
use App\InicioCaja;
use App\Merma;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return view('reportes.index', compact('array_productos', 'array_cajas', 'array_clientes', 'gestiones', 'array_cajas2', 'array_clientes2'));
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

    public function inventario(Request $request)
    {
        $filtro = $request->filtro;
        $productos = Producto::where('status', 1)
            ->orderBy('nombre', 'ASC')
            ->get();
        if ($filtro != 'todos') {
            $productos = Producto::where('status', 1)
                ->where('estado', $filtro)
                ->orderBy('nombre', 'ASC')
                ->get();
        }

        $pdf = PDF::loadView('reportes.inventario', compact('productos'))->setPaper('letter', 'landscape');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Inventario.pdf');
    }

    public function kardex(Request $request)
    {
        $filtro = $request->filtro;
        $producto = $request->producto;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $productos = Producto::where('status', 1)
            ->orderBy('nombre', 'ASC')
            ->get();
        if ($filtro != 'todos') {
            switch ($filtro) {
                case 'producto':
                    if ($producto != '') {
                        $productos = Producto::where('status', 1)
                            ->where('id', $producto)
                            ->orderBy('nombre', 'ASC')
                            ->get();
                    }
                    break;
            }
        }

        $array_kardex = [];
        $array_saldo_anterior = [];
        foreach ($productos as $producto) {
            $kardex = KardexProducto::where('producto_id', $producto->id)->get();
            $array_saldo_anterior[$producto->id] = [
                'sw' => false,
                'saldo_anterior' => []
            ];
            if ($filtro == 'fecha') {
                $kardex = KardexProducto::where('producto_id', $producto->id)
                    ->whereBetween('fecha', [$fecha_ini, $fecha_fin])->get();
                // buscar saldo anterior si existe
                $saldo_anterior = KardexProducto::where('producto_id', $producto->id)
                    ->where('fecha', '<', $fecha_ini)
                    ->orderBy('created_at', 'asc')->get()->last();
                if ($saldo_anterior) {
                    $saldo_c = $saldo_anterior->saldo_c;
                    $saldo_m = $saldo_anterior->saldo_m;
                    $array_saldo_anterior[$producto->id] = [
                        'sw' => true,
                        'saldo_anterior' => [
                            'saldo_c' => $saldo_c,
                            'saldo_m' => $saldo_m,
                        ]
                    ];
                }
            }
            $array_kardex[$producto->id] = $kardex;
        }

        $pdf = PDF::loadView('reportes.kardex', compact('productos', 'array_kardex', 'array_saldo_anterior'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Kardex.pdf');
    }

    public function ventas(Request $request)
    {
        $filtro = $request->filtro;
        $caja = $request->caja;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $ventas = Venta::whereIn('estado', [1, 2])->get();
        $monto_total = Venta::whereIn('estado', [1, 2])->sum('monto_total');
        if ($filtro != 'todos') {
            switch ($filtro) {
                case 'caja':
                    if ($caja != 'todos') {
                        $ventas = Venta::whereIn('estado', [1, 2])
                            ->where('caja_id', $caja)->get();
                        $monto_total = Venta::whereIn('estado', [1, 2])
                            ->where('caja_id', $caja)->sum('monto_total');
                    }
                    break;
                case 'fecha':
                    $ventas = Venta::whereIn('estado', [1, 2])
                        ->whereBetween('caja_id', [$fecha_ini, $fecha_fin])->get();
                    $monto_total = Venta::whereIn('estado', [1, 2])
                        ->whereBetween('caja_id', [$fecha_ini, $fecha_fin])->sum('monto_total');
                    break;
            }
        }

        $pdf = PDF::loadView('reportes.ventas', compact('ventas', 'monto_total'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('ventas.pdf');
    }

    public function cuentas(Request $request)
    {
        $filtro = $request->filtro;
        $cliente = $request->cliente;

        $clientes = Cliente::where('estado', 1)->get();
        if ($filtro != 'todos') {
            switch ($filtro) {
                case 'cliente':
                    if ($cliente != 'todos') {
                        $clientes = Cliente::where('estado', 1)
                            ->where('id', $cliente)->get();
                    }
                    break;
            }
        }
        $array_cuentas = [];
        $array_montos = [];
        foreach ($clientes as $cliente) {
            $array_cuentas[$cliente->id] = [];
            $array_montos[$cliente->id] = [
                'sw' => false,
                'monto_deuda' => 0,
                'saldo_deuda' => 0,
            ];
            $cuentas = CuentaCobrar::where('cliente_id', $cliente->id)
                ->where('status', 1)->get();
            if (count($cuentas) > 0) {
                $array_cuentas[$cliente->id] = $cuentas;
                $array_montos[$cliente->id]['sw'] = true;
                $array_montos[$cliente->id]['monto_deuda'] = CuentaCobrar::where('cliente_id', $cliente->id)->where('status', 1)->sum('monto_deuda');
                $array_montos[$cliente->id]['saldo_deuda'] = CuentaCobrar::where('cliente_id', $cliente->id)->where('status', 1)->sum('saldo');
            }
        }

        $pdf = PDF::loadView('reportes.cuentas', compact('clientes', 'array_cuentas', 'array_montos'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('cuentas.pdf');
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
    public function ventas_diarias_producto(Request $request)
    {
        $fecha = $request->fecha;

        $productos = Producto::where("status", 1)->get();
        $ventas_producto = [];
        foreach ($productos as $producto) {
            $ventas = DB::select("SELECT SUM(vd.cantidad) as cantidad, SUM(vd.monto) as precio, SUM(vd.sub_total) as importe FROM venta_detalles vd INNER JOIN ventas v ON v.id = vd.venta_id WHERE vd.producto_id = $producto->id AND v.fecha_venta = '$fecha' AND v.estado IN (1,2)");
            if (count($ventas) > 0) {
                $ventas_producto[$producto->id] = [
                    "cantidad" => $ventas[0]->cantidad ? $ventas[0]->cantidad : 0,
                    "precio" => $ventas[0]->precio ? $ventas[0]->precio : $producto->precio,
                    "importe" => $ventas[0]->importe ? $ventas[0]->importe : '0.00',
                ];
            } else {
                $ventas_producto[$producto->id] = [
                    "cantidad" => 0,
                    "precio" => 0,
                    "importe" => 0,
                ];
            }
        }

        $pdf = PDF::loadView('reportes.ventas_diarias_producto', compact('productos', 'ventas_producto', "fecha"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('ventas_diarias_producto.pdf');
    }

    public function ventas_semanales_producto(Request $request)
    {
        $aux_ini = $request->fecha_ini;
        $fecha_ini = new DateTime($request->fecha_ini);
        $fecha_fin = new DateTime($request->fecha_fin);
        $diff = $fecha_ini->diff($fecha_fin);
        $total_dias = $diff->days + 1;
        if ($total_dias > 0 && $fecha_ini <= $fecha_fin) {
            // armar semanas
            $total_semanas = round($total_dias / 7, 0, PHP_ROUND_HALF_DOWN);
            $residuo_dias = $total_dias % 7; // se utilizara para aumentar el total de días restantes a la ultima semana
            $fecha_aux_ini = date("Y-m-d", strtotime($aux_ini));

            // lista de productos
            $productos = Producto::where("status", 1)->get();
            $ventas_producto = [];
            for ($i = 1; $i <= $total_semanas; $i++) {
                $fecha_aux_fin = date("Y-m-d", strtotime($fecha_aux_ini . "+6 days"));
                if ($i == $total_semanas && $residuo_dias > 0) {
                    $aumento_dias = 6 + (int)$residuo_dias;
                    $fecha_aux_fin = date("Y-m-d", strtotime($fecha_aux_ini . "+$aumento_dias days"));
                }

                foreach ($productos as $producto) {
                    $ventas_producto[$producto->id][$i] = ['cantidad' => 0];
                    $ventas = DB::select("SELECT SUM(vd.cantidad) as cantidad FROM venta_detalles vd INNER JOIN ventas v ON v.id = vd.venta_id WHERE vd.producto_id = $producto->id AND v.fecha_venta BETWEEN '$fecha_aux_ini' AND '$fecha_aux_fin' AND v.estado IN (1,2)");
                    $ventas_producto[$producto->id][$i] = ['cantidad' => $ventas[0]->cantidad ? $ventas[0]->cantidad : 0];
                }
                $fecha_aux_ini = date("Y-m-d", strtotime($fecha_aux_fin . '+1 days'));
            }

            $pdf = PDF::loadView('reportes.ventas_semanales_producto', compact('total_semanas', 'productos', 'ventas_producto', "aux_ini"))->setPaper('letter', 'landscape');
            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

            return $pdf->stream('ventas_semanales_producto.pdf');
        } else {
            return "Error, no se pudo generar el reporte debido a un error en las fechas";
        }
    }

    public function ventas_mensuales_producto(Request $request)
    {
        $gestion = $request->gestion;
        $meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        $productos = Producto::where("status", 1)->get();
        $ventas_producto = [];
        foreach ($meses as $mes) {
            $fecha_mes = $gestion . '-' . $mes;
            foreach ($productos as $producto) {
                $ventas_producto[$producto->id][$mes] = ['cantidad' => 0];
                $ventas = DB::select("SELECT SUM(vd.cantidad) as cantidad FROM venta_detalles vd INNER JOIN ventas v ON v.id = vd.venta_id WHERE vd.producto_id = $producto->id AND v.fecha_venta LIKE '$fecha_mes%' AND v.estado IN (1,2)");
                $ventas_producto[$producto->id][$mes] = ['cantidad' => $ventas[0]->cantidad ? $ventas[0]->cantidad : 0];
            }
        }

        $ventas = Venta::where('estado', 1)->get();

        $pdf = PDF::loadView('reportes.ventas_mensuales_producto', compact('meses', 'productos', 'ventas_producto'))->setPaper('letter', 'landscape');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('ventas_mensuales_producto.pdf');
    }

    public function ventas_diarias_cajas(Request $request)
    {
        // VERIFICAR SI EXISTE UN INICIO DE CAJA
        $inicio_caja = InicioCaja::existeInicio(date('Y-m-d'), $request->caja_id);
        if (!$inicio_caja) {
            $inicio_caja = InicioCaja::create([
                'caja_id' => $request->caja_id,
                'monto_inicial' => (float)Caja::getSaldo($request->caja_id),
                'fecha_inicio' => date('Y-m-d'),
                'descripcion' => 'APERTURA DE CAJA',
                'user_id' => Auth::user()->id,
                'fecha_registro' => date('Y-m-d'),
                'estado' => 1
            ]);
        }

        $caja_id = $request->caja_id;
        $fecha = $request->fecha;
        $ventas = Venta::where("caja_id", $caja_id)->where("fecha_venta", $fecha)->whereIn("estado", [1, 2])->get();
        $total_ventas = Venta::where("caja_id", $caja_id)->where("fecha_venta", $fecha)->whereIn("estado", [1, 2])->sum("monto_total");

        $inicio_caja = InicioCaja::where("caja_id", $caja_id)->where("fecha_registro", $fecha)->get()->last();
        $ingreso_caja_central = IngresoCaja::where("caja_id", $caja_id)
            ->where("fecha", $fecha)
            ->where("registro_id", 0)
            ->where("tipo_movimiento", "INGRESO")
            ->whereIn("estado", [1, 2])
            ->get()->sum("monto_total");
        $gastos = IngresoCaja::where("caja_id", $caja_id)
            ->where("fecha", $fecha)
            ->where("tipo_movimiento", "EGRESO")
            ->whereIn("sw_egreso", ["GASTO", "COMPRA"])
            ->where("concepto_id", "!=", 0)
            ->whereIn("estado", [1, 2])
            ->get()->sum("monto_total");
        $traspasos_caja_central = IngresoCaja::where("caja_id", $caja_id)
            ->where("fecha", $fecha)
            ->where("tipo_movimiento", "EGRESO")
            ->where("sw_egreso", "EGRESO A CAJA CENTRAL")
            ->whereIn("estado", [1, 2])
            ->where("concepto_id", 0)->get()->sum("monto_total");

        $total_efectivo = (float)$inicio_caja->monto_inicial + (float)$ingreso_caja_central + (float)$total_ventas - (float)$gastos - (float)$traspasos_caja_central;

        $pdf = PDF::loadView('reportes.ventas_diarias_cajas', compact('fecha', 'ventas', 'inicio_caja', 'ingreso_caja_central', 'gastos', 'traspasos_caja_central', 'total_efectivo'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('ventas_diarias_cajas.pdf');
    }
    public function egreso_caja(Request $request)
    {
        $caja_id = $request->caja_id;
        $fecha = $request->fecha;

        $conceptos = Concepto::all();
        $array_detalles = [];
        foreach ($conceptos as $value) {
            $array_detalles[$value->id] = IngresoCaja::where("concepto_id", $value->id)
                ->where("concepto_id", "!=", 0)
                ->where("tipo_movimiento", 'EGRESO')
                ->where("caja_id", $caja_id)
                ->where("fecha", $fecha)
                ->whereIn("estado", [1, 2])
                ->get();
        }

        $pdf = PDF::loadView('reportes.egreso_caja', compact('fecha', 'array_detalles', 'conceptos'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('egreso_caja.pdf');
    }

    public function egresos_caja(Request $request)
    {
        $caja_id = $request->caja_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $conceptos = Concepto::all();
        $array_detalles = [];
        foreach ($conceptos as $value) {
            $array_detalles[$value->id] = IngresoCaja::where("concepto_id", $value->id)
                ->where("concepto_id", "!=", 0)
                ->where("caja_id", $caja_id)
                ->where("tipo_movimiento", 'EGRESO')
                ->whereIn("estado", [1, 2])
                ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
                ->get();
        }

        $pdf = PDF::loadView('reportes.egresos_caja', compact('fecha_ini', 'fecha_fin', 'array_detalles', 'conceptos'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('egresos_caja.pdf');
    }

    public function consumo_diario_clientes(Request $request)
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

        while ($aux_fecha_ini <= $fecha_fin) {
            $dia = ReporteController::getDiaFecha($aux_fecha_ini);
            $dias_fechas[$dia][] = $aux_fecha_ini;

            $aux_fecha_ini = date("Y-m-d", strtotime($aux_fecha_ini . '+1 days'));
        }

        $consumo_clientes = [];
        foreach ($clientes as $cliente) {
            $consumo_clientes[$cliente->id] = [
                '1' => 0, //"LUNES",
                '2' => 0, //"MARTES",
                '3' => 0, //"MIERCOLES",
                '4' => 0, //"JUEVES",
                '5' => 0, //"VIERNES",
                '6' => 0, //"SABADO",
                '0' => 0, //"DOMINGO",
            ];

            foreach ($dias_fechas as $key => $dia_fecha) {
                $total_dia = 0;
                foreach ($dia_fecha as $fecha) {
                    $ventas = DB::select("SELECT SUM(vd.cantidad) as cantidad FROM venta_detalles vd INNER JOIN ventas v ON v.id = vd.venta_id WHERE v.fecha_venta = '$fecha' AND v.cliente_id = $cliente->id AND v.estado IN (1,2)");
                    $total_dia += $ventas[0]->cantidad ? (float)$ventas[0]->cantidad : 0;
                }
                $consumo_clientes[$cliente->id][$key] = $total_dia;
            }
        }

        $fecha = date("Y-m-d");
        $pdf = PDF::loadView('reportes.consumo_diario_clientes', compact('fecha_ini', 'fecha_fin', 'dias_fechas', 'clientes', 'consumo_clientes', 'fecha'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('consumo_diario_clientes.pdf');
    }

    public function consumo_semanal_clientes(Request $request)
    {
        $aux_ini = $request->fecha_ini;
        $fecha_ini = new DateTime($request->fecha_ini);
        $fecha_fin = new DateTime($request->fecha_fin);
        $diff = $fecha_ini->diff($fecha_fin);
        $total_dias = $diff->days + 1;

        if ($total_dias > 0 && $fecha_ini <= $fecha_fin) {
            // armar semanas
            $total_semanas = round($total_dias / 7, 0, PHP_ROUND_HALF_DOWN);
            $residuo_dias = $total_dias % 7; // se utilizara para aumentar el total de días restantes a la ultima semana
            $fecha_aux_ini = date("Y-m-d", strtotime($aux_ini));

            $clientes = Cliente::where('estado', 1)->get();
            $consumo_semanal = [];
            for ($i = 1; $i <= $total_semanas; $i++) {
                $fecha_aux_fin = date("Y-m-d", strtotime($fecha_aux_ini . "+6 days"));
                if ($i == $total_semanas && $residuo_dias > 0) {
                    $aumento_dias = 6 + (int)$residuo_dias;
                    $fecha_aux_fin = date("Y-m-d", strtotime($fecha_aux_ini . "+$aumento_dias days"));
                }

                foreach ($clientes as $cliente) {
                    $consumo_semanal[$cliente->id][$i] = ['cantidad' => 0];
                    $ventas = DB::select("SELECT SUM(vd.cantidad) as cantidad FROM venta_detalles vd INNER JOIN ventas v ON v.id = vd.venta_id WHERE v.cliente_id = $cliente->id AND v.fecha_venta BETWEEN '$fecha_aux_ini' AND '$fecha_aux_fin' AND v.estado IN (1,2)");
                    $consumo_semanal[$cliente->id][$i] = ['cantidad' => $ventas[0]->cantidad ? $ventas[0]->cantidad : 0];
                }
                $fecha_aux_ini = date("Y-m-d", strtotime($fecha_aux_fin . '+1 days'));
            }

            $fecha_ini = $request->fecha_ini;
            $fecha_fin = $request->fecha_fin;

            $pdf = PDF::loadView('reportes.consumo_semanal_clientes', compact('total_semanas', 'clientes', 'consumo_semanal', "fecha_ini", "fecha_fin"))->setPaper('letter', 'portrait');
            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

            return $pdf->stream('consumo_semanal_clientes.pdf');
        } else {
            return "Error, no se pudo generar el reporte debido a un error en las fechas";
        }
    }

    public function consumo_mensual_clientes(Request $request)
    {
        $gestion = $request->gestion;
        $meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        $clientes = Cliente::where('estado', 1)->get();
        $consumo_clientes = [];
        foreach ($meses as $mes) {
            $fecha_mes = $gestion . '-' . $mes;
            foreach ($clientes as $cliente) {
                $consumo_clientes[$cliente->id][$mes] = ['cantidad' => 0];
                $ventas = DB::select("SELECT SUM(vd.cantidad) as cantidad FROM venta_detalles vd INNER JOIN ventas v ON v.id = vd.venta_id WHERE v.cliente_id = $cliente->id AND v.fecha_venta LIKE '$fecha_mes%' AND v.estado IN (1,2)");
                $consumo_clientes[$cliente->id][$mes] = ['cantidad' => $ventas[0]->cantidad ? $ventas[0]->cantidad : 0];
            }
        }

        $ventas = Venta::whereIn('estado', [1, 2])->get();

        $pdf = PDF::loadView('reportes.consumo_mensual_clientes', compact('meses', 'clientes', 'consumo_clientes'))->setPaper('letter', 'landscape');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('consumo_mensual_clientes.pdf');
    }

    public function ventas_diarias_credito(Request $request)
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

        while ($aux_fecha_ini <= $fecha_fin) {
            $dia = ReporteController::getDiaFecha($aux_fecha_ini);
            $dias_fechas[$dia][] = $aux_fecha_ini;

            $aux_fecha_ini = date("Y-m-d", strtotime($aux_fecha_ini . '+1 days'));
        }

        $ventas_credito = [];
        foreach ($clientes as $cliente) {
            $ventas_credito[$cliente->id] = [
                '1' => 0, //"LUNES",
                '2' => 0, //"MARTES",
                '3' => 0, //"MIERCOLES",
                '4' => 0, //"JUEVES",
                '5' => 0, //"VIERNES",
                '6' => 0, //"SABADO",
                '0' => 0, //"DOMINGO",
            ];

            foreach ($dias_fechas as $key => $dia_fecha) {
                $total_dia = 0;
                foreach ($dia_fecha as $fecha) {
                    // POR COBRAR
                    $ventas = DB::select("SELECT SUM(monto_total) as monto_total FROM ventas WHERE fecha_venta = '$fecha' AND cliente_id = $cliente->id AND tipo_venta='POR COBRAR' AND estado IN (1,2)");
                    $total_dia += $ventas[0]->monto_total ? (float)$ventas[0]->monto_total : 0;
                }
                $ventas_credito[$cliente->id][$key] = $total_dia;
            }
        }

        $fecha = date("Y-m-d");
        $pdf = PDF::loadView('reportes.ventas_diarias_credito', compact('fecha_ini', 'fecha_fin', 'dias_fechas', 'clientes', 'ventas_credito', 'fecha'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('ventas_diarias_credito.pdf');
    }

    public function ventas_semanales_credito(Request $request)
    {
        $aux_ini = $request->fecha_ini;
        $fecha_ini = new DateTime($request->fecha_ini);
        $fecha_fin = new DateTime($request->fecha_fin);
        $diff = $fecha_ini->diff($fecha_fin);
        $total_dias = $diff->days + 1;

        if ($total_dias > 0 && $fecha_ini <= $fecha_fin) {
            // armar semanas
            $total_semanas = round($total_dias / 7, 0, PHP_ROUND_HALF_DOWN);
            $residuo_dias = $total_dias % 7; // se utilizara para aumentar el total de días restantes a la ultima semana
            $fecha_aux_ini = date("Y-m-d", strtotime($aux_ini));

            $clientes = Cliente::where('estado', 1)->get();
            $ventas_creditos_semanal = [];
            for ($i = 1; $i <= $total_semanas; $i++) {
                $fecha_aux_fin = date("Y-m-d", strtotime($fecha_aux_ini . "+6 days"));
                if ($i == $total_semanas && $residuo_dias > 0) {
                    $aumento_dias = 6 + (int)$residuo_dias;
                    $fecha_aux_fin = date("Y-m-d", strtotime($fecha_aux_ini . "+$aumento_dias days"));
                }

                foreach ($clientes as $cliente) {
                    $ventas_creditos_semanal[$cliente->id][$i] = ['monto_total' => 0];
                    $ventas = DB::select("SELECT SUM(monto_total) as monto_total FROM ventas WHERE cliente_id = $cliente->id AND fecha_venta BETWEEN '$fecha_aux_ini' AND '$fecha_aux_fin' AND tipo_venta='POR COBRAR' AND estado IN (1,2)");
                    $ventas_creditos_semanal[$cliente->id][$i] = ['monto_total' => $ventas[0]->monto_total ? $ventas[0]->monto_total : 0];
                }
                $fecha_aux_ini = date("Y-m-d", strtotime($fecha_aux_fin . '+1 days'));
            }

            $fecha_ini = $request->fecha_ini;
            $fecha_fin = $request->fecha_fin;

            $pdf = PDF::loadView('reportes.ventas_semanales_credito', compact('total_semanas', 'clientes', 'ventas_creditos_semanal', "fecha_ini", "fecha_fin"))->setPaper('letter', 'portrait');
            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

            return $pdf->stream('ventas_semanales_credito.pdf');
        } else {
            return "Error, no se pudo generar el reporte debido a un error en las fechas";
        }
    }

    public function ventas_mensuales_credito(Request $request)
    {
        $gestion = $request->gestion;
        $meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        $clientes = Cliente::where('estado', 1)->get();
        $ventas_mensuales = [];
        foreach ($meses as $mes) {
            $fecha_mes = $gestion . '-' . $mes;
            foreach ($clientes as $cliente) {
                $ventas_mensuales[$cliente->id][$mes] = ['monto_total' => 0];
                $ventas = DB::select("SELECT SUM(monto_total) as monto_total FROM ventas WHERE cliente_id = $cliente->id AND fecha_venta LIKE '$fecha_mes%' AND tipo_venta='POR COBRAR' AND estado IN (1,2)");
                $ventas_mensuales[$cliente->id][$mes] = ['monto_total' => $ventas[0]->monto_total ? $ventas[0]->monto_total : 0];
            }
        }

        $ventas = Venta::whereIn('estado', [1, 2])->get();

        $pdf = PDF::loadView('reportes.ventas_mensuales_credito', compact('meses', 'clientes', 'ventas_mensuales'))->setPaper('letter', 'landscape');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('ventas_mensuales_credito.pdf');
    }

    public function cuentas_cobrar_fecha(Request $request)
    {
        $fecha = $request->fecha;

        $cuentas_cobrar = CuentaCobrar::select("cuenta_cobrars.*")
            ->join("ventas", "ventas.id", "=", "cuenta_cobrars.venta_id")
            ->where("cuenta_cobrars.saldo", ">", 0)
            ->where("ventas.fecha_venta", $fecha)
            ->get();

        $pdf = PDF::loadView('reportes.cuentas_cobrar_fecha', compact('cuentas_cobrar', "fecha"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('cuentas_cobrar_fecha.pdf');
    }

    public function cuentas_cobrar_rango_fecha(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $cuentas_cobrar = CuentaCobrar::select("cuenta_cobrars.*")
            ->join("ventas", "ventas.id", "=", "cuenta_cobrars.venta_id")
            ->where("cuenta_cobrars.saldo", ">", 0)
            ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
            ->get();

        $pdf = PDF::loadView('reportes.cuentas_cobrar_rango_fecha', compact('cuentas_cobrar', "fecha_ini", "fecha_fin"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('cuentas_cobrar_rango_fecha.pdf');
    }

    public function estado_cuenta_cliente(Request $request)
    {
        $cliente_id = $request->cliente_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $cliente = Cliente::find($cliente_id);
        $cuentas_cobrar = CuentaCobrar::select("cuenta_cobrars.*")
            ->join("ventas", "ventas.id", "=", "cuenta_cobrars.venta_id")
            ->where("ventas.cliente_id", $cliente_id)
            ->whereBetween("ventas.fecha_venta", [$fecha_ini, $fecha_fin])
            ->whereIn("ventas.estado", [1, 2])
            ->get();

        $pdf = PDF::loadView('reportes.estado_cuenta_cliente', compact('cliente', 'cuentas_cobrar', "fecha_ini", "fecha_fin"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('estado_cuenta_cliente.pdf');
    }

    public function detalle_inventario_producto(Request $request)
    {
        $filtro = $request->filtro;
        $producto = $request->producto;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $productos = Producto::where('status', 1)
            ->orderBy('nombre', 'ASC')
            ->get();
        if ($filtro != 'todos') {
            switch ($filtro) {
                case 'producto':
                    if ($producto != '') {
                        $productos = Producto::where('status', 1)
                            ->where('id', $producto)
                            ->orderBy('nombre', 'ASC')
                            ->get();
                    }
                    break;
            }
        }

        $array_kardex = [];
        $array_saldo_anterior = [];
        foreach ($productos as $producto) {
            $kardex = KardexProducto::where('producto_id', $producto->id)->get();
            $array_saldo_anterior[$producto->id] = [
                'sw' => false,
                'saldo_anterior' => []
            ];
            if ($filtro == 'fecha') {
                $kardex = KardexProducto::where('producto_id', $producto->id)
                    ->whereBetween('fecha', [$fecha_ini, $fecha_fin])->get();
                // buscar saldo anterior si existe
                $saldo_anterior = KardexProducto::where('producto_id', $producto->id)
                    ->where('fecha', '<', $fecha_ini)
                    ->orderBy('created_at', 'asc')->get()->last();
                if ($saldo_anterior) {
                    $saldo_c = $saldo_anterior->saldo_c;
                    $saldo_m = $saldo_anterior->saldo_m;
                    $array_saldo_anterior[$producto->id] = [
                        'sw' => true,
                        'saldo_anterior' => [
                            'saldo_c' => $saldo_c,
                            'saldo_m' => $saldo_m,
                        ]
                    ];
                }
            }
            $array_kardex[$producto->id] = $kardex;
        }
        // return $array_saldo_anterior;

        $pdf = PDF::loadView('reportes.detalle_inventario_producto', compact('productos', 'array_kardex', 'array_saldo_anterior'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('detalle_inventario_producto.pdf');
    }

    public function cuenta_pagar(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $cuenta_pagars = CuentaPagar::whereBetween("fecha_registro", [$fecha_ini, $fecha_fin])->get();
        $pdf = PDF::loadView('reportes.cuenta_pagar', compact("cuenta_pagars", "fecha_ini", "fecha_fin"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('cuenta_pagar.pdf');
    }

    public function saldo_producto(Request $request)
    {
        $fecha = $request->fecha;
        $productos = Producto::where("status", 1)->get();
        $pdf = PDF::loadView('reportes.saldo_producto', compact('productos'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('saldo_producto.pdf');
    }

    public function resultado_ventas(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $ventas = IngresoCaja::where("registro_id", "!=", 0)
            ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
            ->whereIn("estado", [1, 2])->sum("monto_total");

        $inventario = 0;
        $productos = Producto::where("status", 1)->get();

        foreach ($productos as $value) {
            $inventario += ((float)$value->stock_actual * (float)$value->precio);
        }
        $compras = CajaCentral::where("sw_egreso", "COMPRA")->sum("monto");

        $resultado = number_format($ventas + $inventario - $compras, 2);

        $pdf = PDF::loadView('reportes.resultado_ventas', compact("ventas", "inventario", "compras", "resultado"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('resultado_ventas.pdf');
    }

    public function mermas(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $mermas = Merma::whereBetween("fecha", [$fecha_ini, $fecha_fin])->get();
        $pdf = PDF::loadView('reportes.mermas', compact("mermas", "fecha_ini", "fecha_fin"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('mermas.pdf');
    }

    public function descuento_ventas(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $descuentos_cliente = [];

        $descuento_ventas = DB::select("SELECT SUM(vd.descuento) as descuento,
        v.fecha_venta as fecha,
        c.nombre as cliente 
        FROM venta_detalles vd 
        JOIN ventas v ON vd.venta_id=v.id 
        JOIN clientes c ON c.id = v.cliente_id 
        WHERE v.fecha_venta BETWEEN '$fecha_ini' AND '$fecha_fin' AND v.estado IN (1,2) GROUP BY v.id
        ORDER BY v.fecha_venta DESC");

        $pdf = PDF::loadView('reportes.descuento_ventas', compact("descuento_ventas", "fecha_ini", "fecha_fin"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('descuento_ventas.pdf');
    }

    // FUNCIONES
    public static function getDiaFecha($fecha)
    {
        return date("w", strtotime($fecha));
    }
}
