<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OrdenVenta</title>
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

        .elemento.logo img{
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
            ORDEN DE VENTA
        </div>
        <div class="elemento detalle izquierda">
            Nro: {{ $nro_factura }} <br>
            Fecha: {{ date('d/m/Y', strtotime($venta->fecha_venta)) }} <br>
            Hora: {{ $venta->hora_venta }} <br>
            Cliente: {{ $venta->factura->cliente }} <br>
            CI/NIT: {{ $venta->factura->nit }} <br>
            Caja: {{ $venta->user->name }} <br>
            Tipo de Pago: {{ $venta->tipo_venta }} <br>
        </div>
        <div class="elemento">
            DETALLE
        </div>
        <div class="cobro">
            <table>
                <tr class="punteado">
                    <td class="centreado">CANTIDAD</td>
                    <td class="centreado">PRODUCTO</td>
                    <td class="centreado">CU</td>
                    <td class="centreado">TOTAL</td>
                </tr>
                @foreach ($venta->detalle as $value)
                    <tr>
                        <td class="centreado">{{ $value->cantidad }}</td>
                        <td class="izquierda">{{ $value->producto->nombre }}</td>
                        <td class="centreado">{{ $value->monto }}</td>
                        <td class="centreado">{{ $value->sub_total }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="bold elemento">Total Final: {{ $venta->monto_total }}</td>
                </tr>
            </table>
        </div>
        <div class="izquierda literal">
            Son: {{ $literal }}
        </div>
    </div>
</body>

</html>
