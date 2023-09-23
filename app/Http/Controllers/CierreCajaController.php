<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CierreCaja;
use App\Caja;
use App\CajaCentral;
use App\IngresoCaja;
use App\InicioCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class CierreCajaController extends Controller
{
    public function index()
    {
        $cierre_cajas = [];
        if (Auth::user()->tipo == 'CAJA') {
            $cierre_cajas = CierreCaja::whereIn('estado', [1, 2])
                ->where('caja_id', Auth::user()->caja->caja_id)
                ->orderBy("created_at", "desc")
                ->get();
        } else {
            $cierre_cajas = CierreCaja::where('estado', 1)
                ->get();
        }
        return view('cierre_cajas.index', compact('cierre_cajas'));
    }

    public function create()
    {
        $cajas = Caja::where('estado', 1)->get();
        $array_cajas[''] = "Seleccione...";
        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }

        $monto = 0;
        if (Auth::user()->tipo == 'CAJA') {
            $monto = Caja::getSaldo(Auth::user()->caja->caja_id);
        }

        $monto_banco = Caja::getSumaBancos(Auth::user()->caja->caja_id);
        $monto_otros = Caja::getSumaOtros(Auth::user()->caja->caja_id);

        return view('cierre_cajas.create', \compact('monto', 'array_cajas', 'monto_banco', 'monto_otros'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $cierre_caja = CierreCaja::existeCierre($request->fecha_cierre);
            if ($cierre_caja) {
                $cierre_caja->estado = 2;
                $cierre_caja->save();
            }

            if (!isset($request->user_id)) {
                $request["user_id"] = Auth::user()->id;
            }
            if (!isset($request->fecha_cierre)) {
                $request["fecha_cierre"] = date("Y-m-d");
            }
            if (!isset($request->fecha_registro)) {
                $request["fecha_registro"] = date("Y-m-d");
            }
            if (!isset($request->estado)) {
                $request["estado"] = 1;
            }
            // VERIFICAR SI EXISTE UN INICIO DE CAJA
            $inicio_caja = InicioCaja::existeInicio(date('Y-m-d'), Auth::user()->caja->caja_id);
            if (!$inicio_caja) {
                $inicio_caja = InicioCaja::create([
                    'caja_id' => Auth::user()->caja->caja_id,
                    'monto_inicial' => (float)Caja::getSaldo(Auth::user()->caja->caja_id),
                    'fecha_inicio' => date('Y-m-d'),
                    'descripcion' => 'APERTURA DE CAJA',
                    'user_id' => Auth::user()->id,
                    'fecha_registro' => date('Y-m-d'),
                    'estado' => 1
                ]);
            }
            $request["inicio_caja_id"] = $inicio_caja->id;

            $monto = Caja::getSaldo(Auth::user()->caja->caja_id);
            $monto_banco = Caja::getSumaBancos(Auth::user()->caja->caja_id);
            $monto_otros = Caja::getSumaOtros(Auth::user()->caja->caja_id);
            $request["monto_total"] = $monto;
            $nuevo_cierre = CierreCaja::create(array_map('mb_strtoupper', $request->all()));

            $inicio_caja->descripcion = "CAMBIO POR CIERRE DE CAJA";
            $inicio_caja->estado = 2;
            $inicio_caja->save();
            // REGISTRA COMO EGRESO EL CIERRE
            $egreso = IngresoCaja::create([
                'caja_id' => $nuevo_cierre->caja_id,
                "inicio_caja_id" => $inicio_caja->id,
                'concepto_id' => 0,
                'tipo_movimiento' => "EGRESO",
                "tipo" => "EGRESO POR CIERRE DE CAJA",
                "registro_id" => 0,
                "monto_total" => $nuevo_cierre->monto_total,
                "fecha" => $nuevo_cierre->fecha_cierre,
                "hora" => date("H:i:s"),
                "estado" => "2",
                "user_id" => Auth::user()->id,
            ]);

            // REGISTRAR EN CAJA CENTRAL COMO INGRESO
            CajaCentral::create([
                'fecha' => $egreso->fecha,
                'monto' => $monto_otros,
                'descripcion' => 'INGRESO POR CIERRE DE LA CAJA ' . $egreso->caja->nombre,
                "concepto_id" => 0,
                'tipo' => "INGRESO",
                "tipo_transaccion" => "CAJA",
                'fecha_registro' => date('Y-m-d'),
            ]);

            // REGISTRAR EN CAJA CENTRAL COMO INGRESO
            CajaCentral::create([
                'fecha' => $egreso->fecha,
                'monto' => $monto_banco,
                'descripcion' => 'INGRESO POR CIERRE DE LA CAJA ' . $egreso->caja->nombre,
                "concepto_id" => 0,
                'tipo' => "INGRESO",
                "tipo_transaccion" => "BANCO",
                'fecha_registro' => date('Y-m-d'),
            ]);

            // VACIAR VENTAS Y CAJA
            DB::select("UPDATE ingreso_cajas SET estado=2 WHERE caja_id=$nuevo_cierre->caja_id");
            DB::select("UPDATE ventas SET estado=2 WHERE caja_id=$nuevo_cierre->caja_id AND saldo=0");
            DB::commit();
            return redirect()->route('ingreso_cajas.index', $nuevo_cierre->caja_id)->with('bien', 'Registro realizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ingreso_cajas.index', $nuevo_cierre->caja_id)->with('error', $e->getMessage());
        }
    }

    public function edit(CierreCaja $cierre_caja)
    {
        $cajas = Caja::where('estado', 1)->get();
        $array_cajas[''] = "Seleccione...";
        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }
        return view('cierre_cajas.edit', compact('cierre_caja', 'array_cajas'));
    }

    public function update(CierreCaja $cierre_caja, Request $request)
    {
        $cierre_caja->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('cierre_cajas.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(CierreCaja $cierre_caja)
    {
        return 'mostrar cargo';
    }

    public function destroy(CierreCaja $cierre_caja)
    {
        $cierre_caja->estado = 0;
        $cierre_caja->save();
        return redirect()->route('cierre_cajas.index')->with('bien', 'Registro eliminado correctamente');
    }

    public function getUltimoMontoCaja(Request $request)
    {
        $caja = Caja::find($request->caja_id);
        $monto = IngresoCaja::where('caja_id', $caja->id)
            ->where('fecha', $request->fecha)
            ->sum('monto_total');

        $monto = Caja::getSaldoFecha($caja->id, $request->fecha);

        return response()->JSON([
            'sw' => true,
            'monto' => $monto
        ]);
    }

    public function pdf(CierreCaja $cierre_caja)
    {
        $caja_id = $cierre_caja->caja_id;
        $inicio_caja_id = $cierre_caja->inicio_caja_id;
        // OBTENER LOS REGISTROS
        $ingreso_cajas = IngresoCaja::where("inicio_caja_id", $inicio_caja_id)
            ->where("tipo", "!=", "EGRESO POR CIERRE DE CAJA")
            ->where("caja_id", $caja_id)
            ->orderBy("id", "asc")
            ->get();

        $pdf = PDF::loadView('cierre_cajas.pdf', compact('cierre_caja', "ingreso_cajas"))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('ComprobanteCierreCaja.pdf');
    }
}
