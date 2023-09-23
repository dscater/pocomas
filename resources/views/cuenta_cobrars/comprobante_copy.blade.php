<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Comprobante</title>
    <style type="text/css">
        * {
            /* font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; */
        }

        @page {
            margin-left: 0;
            margin-top: 0cm;
            margin-right: 0cm;
            margin-bottom: 0cm;
        }

        body {
            position: relative;
        }

        /* FACURA */
        .contenedor_factura {
            font-size: 0.9em;
            width: 7cm !important;
            padding-top: 15px;
            padding-bottom: 15px;
            position: abosulte;
        }

        .elemento {
            text-align: center;
        }

        .elemento.logo img {
            width: 60%;
        }

        .separador {
            padding: 0px;
            margin: 0px;
        }

        .fono,
        .lp {
            font-size: 0.75em;
        }

        .txt_fo {
            margin-top: 3px;
            border-top: dashed 1px black;
        }

        .detalle {
            border-top: dashed 1px black;
            border-bottom: dashed 1px black;
        }

        .act_eco {
            font-size: 0.73em;
        }

        .info1 {
            text-align: center;
            font-weight: bold;
            font-size: 0.75em;
        }

        .info2 {
            text-align: center;
            font-weight: bold;
            font-size: 0.7em;
        }

        .izquierda {
            text-align: left;
            padding-left: 5px;
        }

        .derecha {
            text-align: right;
            padding-right: 5px;
        }

        .informacion {
            padding: 5px;
            width: 100%;
        }

        .bold {
            font-weight: bold;
        }

        .cobro {
            width: 100%;
            padding: 5px;
        }

        .cobro table {
            width: 100%;
        }

        .centreado {
            text-align: center;
        }

        .cobro table tr td {
            font-size: 0.9em;
        }

        .literal {
            font-size: 0.7em;
        }

        .cod_control,
        .fecha_emision {
            font-size: 0.9em;
        }

        .cobro table {
            border-collapse: collapse;
        }

        .cobro table tr.punteado td {
            border-top: dashed 1px black;
            border-bottom: dashed 1px black;
        }

        .qr img {
            width: 160px;
            height: 160px;
        }

        .total {
            font-size: 0.9em !important;
        }
    </style>
</head>

<body>
    @php
        $empresa = App\RazonSocial::first();
    @endphp
    <div class="contenedor_factura">
        <div class="elemento logo">
            <img src="{{ asset('imgs/' . $empresa->logo) }}" alt="Logo">
        </div>
        <div class="elemento nom_empresa">
            "{{ $empresa->nombre }}"
        </div>
        <div class="elemento direccion">
            Dirección: {{ $empresa->dir }}
        </div>
        <div class="elemento txt_fo">
            COMPROBANTE
        </div>
        <div class="elemento detalle izquierda">
            Fecha: {{ date('d/m/Y', strtotime($cuenta_cobrar->created_at)) }} <br>
            Hora: {{ date('H:i:s', strtotime($cuenta_cobrar->created_at)) }} <br>
            Cliente: {{ $cuenta_cobrar->venta->factura->cliente }} <br>
            CI/NIT: {{ $cuenta_cobrar->venta->factura->nit }} <br>
            Caja: {{ Auth::user()->name }} <br>
        </div>
        <div class="elemento">
            DETALLE
        </div>
        <div class="cobro">
            <table>
                <tr>
                    <td class="centreado">CUENTA POR COBRAR</td>
                </tr>
                <tr>
                    <td colspan="4" class="bold elemento">Monto Total: {{ $cuenta_cobrar->monto_deuda }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="bold elemento">Saldo Restante: {{ $cuenta_cobrar->saldo }}</td>
                </tr>
            </table>
        </div>
        <div class="izquierda literal">
            Son: {{ $literal }}
        </div>
    </div>
</body>

</html>
