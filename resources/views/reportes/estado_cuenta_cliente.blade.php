<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>EstadoCuentaCliente</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 2cm;
            margin-bottom: 1cm;
            margin-left: 1.5cm;
            margin-right: 0.5cm;
            border: 5px solid blue;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }

        table thead tr th,
        tbody tr td {
            word-wrap: break-word;
            font-size: 0.63em;
        }

        .encabezado {
            width: 100%;
        }

        .logo img {
            position: absolute;
            width: 200px;
            height: 90px;
            top: -40px;
            left: 0px;
        }

        h2.titulo {
            width: 450px;
            margin: auto;
            margin-top: 15px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14pt;
        }

        .texto {
            width: 250px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fecha {
            width: 250px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: normal;
            font-size: 0.85em;
        }

        .total {
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
        }

        table thead {
            background: rgb(236, 236, 236)
        }

        table thead tr th {
            padding: 3px;
            font-size: 0.7em;
        }

        table tbody tr td {
            padding: 3px;
            font-size: 0.65em;
        }

        .centreado {
            padding-left: 0px;
            text-align: center;
        }

        .datos {
            margin-left: 15px;
            border-top: solid 1px;
            border-collapse: collapse;
            width: 250px;
        }

        .txt {
            font-weight: bold;
            text-align: right;
            padding-right: 5px;
        }

        .txt_center {
            font-weight: bold;
            text-align: center;
        }

        .cumplimiento {
            position: absolute;
            width: 150px;
            right: 0px;
            top: 86px;
        }

        .b_top {
            border-top: solid 1px black;
        }

        .gray {
            background: rgb(202, 202, 202);
        }

        .img_celda img {
            width: 45px;
        }

        .derecha {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .border_bottom {
            border-bottom: solid 1px black;
        }

        .border_top {
            border-top: solid 1px black;
        }
    </style>
</head>

<body>
    @php
        $array_dias = ['0' => 'Domingo', '1' => 'Lunes', '2' => 'Martes', '3' => 'Miércoles', '4' => 'Jueves', '5' => 'Viernes', '6' => 'Sábado'];
        $array_meses = ['01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo', '06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'];
    @endphp
    <div class="encabezado">
        <div class="logo">
            <img src="{{ asset('imgs/' . App\RazonSocial::first()->logo) }}">
        </div>
        <h2 class="titulo">
            {{ App\RazonSocial::first()->nombre }}
        </h2>
        <h4 class="texto">ESTADO DE CUENTA POR CLIENTE</h4>
        <h4 class="fecha">{{ $array_dias[date('w')] }}, {{ date('d') }} de
            {{ $array_meses[date('m')] }} de {{ date('Y') }}</h4>
        <h4 class="fecha">(Expresado en bolivianos)</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th class="border_bottom border_top">CLIENTE:</th>
                <th class="border_bottom border_top" colspan="9" style="text-align: left;">{{ $cliente->nombre }}</th>
            </tr>
            <tr>
                <th class="border_bottom">FECHA</th>
                <th class="border_bottom">PRODUCTO</th>
                <th class="border_bottom">CANTIDAD KILOS</th>
                <th class="border_bottom">CANTIDAD CERDO</th>
                <th class="border_bottom">P/U</th>
                <th class="border_bottom">BS.</th>
                <th class="border_bottom">DESCUENTO</th>
                <th class="border_bottom">BS.</th>
                <th class="border_bottom">CANCELADO</th>
                <th class="border_bottom">SALDO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_sd = 0;
                $total_dc = 0;
                $total_cancelado = 0;
                $total_saldo = 0;
            @endphp
            @foreach ($cuentas_cobrar as $cuenta_cobrar)
                @foreach ($cuenta_cobrar->cuenta_cobrar_detalles as $detalle)
                    <tr>
                        <td class="centreado">{{ date('d/m/Y', strtotime($cuenta_cobrar->venta->fecha_venta)) }}</td>
                        <td class="centreado">{{ $detalle->venta_detalle->producto->nombre }}</td>
                        <td class="centreado">{{ $detalle->venta_detalle->cantidad_kilos }}</td>
                        <td class="centreado">{{ $detalle->venta_detalle->cantidad }}</td>
                        <td class="centreado">{{ $detalle->venta_detalle->producto->precio }}</td>
                        <td class="centreado">{{ $detalle->venta_detalle->total_sd }}</td>
                        <td class="centreado">{{ $detalle->venta_detalle->descuento }}</td>
                        <td class="centreado">{{ $detalle->venta_detalle->sub_total }}</td>
                        <td class="centreado">{{ $detalle->cancelado }}</td>
                        <td class="centreado">{{ $detalle->saldo }}</td>
                    </tr>
                    @php
                        $total_sd += (float) $detalle->venta_detalle->total_sd;
                        $total_dc += (float) $detalle->venta_detalle->sub_total;
                        $total_cancelado += (float) $detalle->cancelado;
                        $total_saldo += (float) $detalle->saldo;
                    @endphp
                @endforeach
            @endforeach
            <tr>
                <td class="bold derecha border_top" colspan="5">TOTAL EFECTIVO</td>
                <td class="centreado border_top">{{ number_format($total_sd, 2) }}</td>
                <td class="border_top"></td>
                <td class="centreado border_top">{{ number_format($total_dc, 2) }}</td>
                <td class="centreado border_top">{{ number_format($total_cancelado, 2) }}</td>
                <td class="centreado border_top">{{ number_format($total_saldo, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
