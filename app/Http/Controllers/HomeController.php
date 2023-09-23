<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\DatosUsuario;
use App\Producto;
use App\Cliente;
use App\Caja;
use App\Venta;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $usuarios = count($usuarios = DatosUsuario::select('datos_usuarios.*')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.status', 1)
            ->get());

        $minimos = [];
        $productos = Producto::where('status', 1)->get();
        if (Auth::user()->tipo != 'CAJA') {
            foreach ($productos as $p) {
                if ($p->stock_actual <= $p->stock_minimo) {
                    $minimos[] = $p;
                }
            }
        }

        $productos = count($productos);

        $clientes = count(Cliente::where('estado', 1)->get());

        $cajas = count(Caja::where('estado', 1)->get());

        $nro_ventas = 0;
        $total_ventas = 0;
        if (Auth::user()->tipo == 'CAJA') {
            $ventas = Venta::where('estado', 1)
                ->where('caja_id', Auth::user()->caja->caja_id)
                ->where('fecha_registro', date('Y-m-d'))->get();

            $nro_ventas = \count($ventas);
            $total_ventas = Venta::where('estado', 1)
                ->where('caja_id', Auth::user()->caja->caja_id)
                ->where('fecha_registro', date('Y-m-d'))->sum('monto_total');
        }

        return view('home', compact('usuarios', 'productos', 'clientes', 'cajas', 'nro_ventas', 'total_ventas', 'minimos'));
    }
}
