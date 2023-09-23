<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InicioCaja;
use App\Caja;
use App\DatosUsuario;
use App\IngresoCaja;
use Illuminate\Support\Facades\Auth;

class InicioCajaController extends Controller
{
    public function index()
    {
        $inicio_cajas = InicioCaja::where('estado', 1)->get();
        return view('inicio_cajas.index', compact('inicio_cajas'));
    }

    public function create()
    {
        $cajas = Caja::where('estado', 1)->get();
        $usuarios = DatosUsuario::select('datos_usuarios.*')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.status', 1)
            ->where('users.tipo', 'CAJA')
            ->get();
        $array_cajas[''] = "Seleccione...";
        $array_users[''] = "Seleccione...";

        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }
        foreach ($usuarios as $value) {
            $array_users[$value->user_id] =  $value->nombre . ' ' . $value->paterno . ' ' . $value->materno;
        }
        return view('inicio_cajas.create', \compact('array_cajas', 'array_users'));
    }

    public function store(Request $request)
    {
        // VERIFICAR SI EXISTE UN INICIO DE CAJA
        $inicio_caja = InicioCaja::existeInicio(date('Y-m-d'), Auth::user()->caja->caja_id);
        if (!$inicio_caja) {
            $inicio_caja = InicioCaja::create([
                'caja_id' => Auth::user()->caja->caja_id,
                'monto_inicial' => (float)Caja::getSaldo(Auth::user()->caja->caja_id),
                'fecha_inicio' => date('Y-m-d'),
                'descripcion' => 'APERTURA DE CAJA POR VENTA EN CAJA',
                'user_id' => Auth::user()->id,
                'fecha_registro' => date('Y-m-d'),
                'estado' => 1
            ]);
        }

        $request['fecha_registro'] = date('Y-m-d');
        $request['estado'] = 1;
        $comprueba = InicioCaja::where('caja_id', $request->caja_id)
            ->where('fecha_inicio', $request->fecha_inicio)
            ->get()
            ->first();
        if ($comprueba) {
            return redirect()->route('inicio_cajas.index')->with('error', 'Ya existe un registro de la caja en la fecha seleccionada (' . $request->fecha_inicio . ')');
        } else {

            // REGISTRA EL INICIO
            $nuevo_inicio = InicioCaja::create(array_map('mb_strtoupper', $request->all()));

            // REGISTRA COMO INGRESO EL MONTO DE INICIO
            IngresoCaja::create([
                'caja_id' => $nuevo_inicio->caja_id,
                "inicio_caja_id" => $inicio_caja->id,
                'concepto_id' => 0,
                'tipo_movimiento' => "INGRESO",
                "tipo" => "INGRESO POR INICIO DE CAJA",
                "registro_id" => 0,
                "monto_total" => $nuevo_inicio->monto_inicial,
                "fecha" => $nuevo_inicio->fecha,
                "hora" => date("H:i:s"),
                "user_id" => Auth::user()->id,
            ]);

            return redirect()->route('inicio_cajas.index')->with('bien', 'Registro realizado con éxito');
        }
    }

    public function edit(InicioCaja $inicio_caja)
    {
        $cajas = Caja::where('estado', 1)->get();
        $array_cajas[''] = "Seleccione...";
        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }

        $usuarios = DatosUsuario::select('datos_usuarios.*')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.status', 1)
            ->where('users.tipo', 'CAJA')
            ->get();
        foreach ($usuarios as $value) {
            $array_users[$value->user_id] =  $value->nombre . ' ' . $value->paterno . ' ' . $value->materno;
        }
        return view('inicio_cajas.edit', compact('inicio_caja', 'array_cajas', 'array_users'));
    }

    public function update(InicioCaja $inicio_caja, Request $request)
    {
        $inicio_caja->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('inicio_cajas.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(InicioCaja $inicio_caja)
    {
        return 'mostrar cargo';
    }

    public function destroy(InicioCaja $inicio_caja)
    {
        $inicio_caja->estado = 0;
        $inicio_caja->save();
        return redirect()->route('inicio_cajas.index')->with('bien', 'Registro eliminado correctamente');
    }
}
