<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resultado de la operación</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 1cm;
            margin-bottom: 1cm;
            margin-left: 2.5cm;
            margin-right: 1cm;
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
            height: 90px;
            top: -20px;
            left: -20px;
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
            width: 350px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fecha {
            width: 350px;
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

        table thead tr th {
            padding: 3px;
            font-size: 0.7em;
        }

        table tbody tr td {
            padding: 3px;
            font-size: 0.55em;
        }

        .centreado {
            padding-left: 0px;
            text-align: center;
        }

        .datos {
            margin-left: 15px;
            border-top: solid 1px;
            border-collapse: collapse;
            width: 350px;
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

        .txt_rojo {}

        .img_celda img {
            width: 45px;
        }

        .derecha {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .normal {
            font-weight: normal;
        }

        .seccion {
            margin-bottom: 5px;
            width: 80%;
        }

        .seccion span {
            float: right;
        }

        .info {
            margin-top: 5px;
            padding-left: 60px;
        }

        .info table {
            width: 90%;
        }

        .border-top {
            border-top: solid 1px black;
        }

        .border-bottom {
            border-bottom: solid 1px black;
        }

        .border-bottom2 {
            border-bottom: double 3px black;
        }

        .bold {
            font-weight: bold;
        }

        .text-md {
            font-size: 1em;
        }

        .text-right {
            text-align: right;
        }

        .col1 {
            width: 60%;
        }

        .info_monto1 {
            width: 50%;
        }

        .info_monto1 span {
            font-weight: bold;
            float: right;
        }

        .total1 {
            text-align: right;
            display: inline-block;
            border-top: solid 0.7px black;
            border-bottom: solid 0.7px black;
            width: 110px;
        }

        .total2 {
            text-align: right;
            display: inline-block;
            border-top: solid 0.7px black;
            border-bottom: double 5px black;
            width: 110px;
        }

        .mb-0 {
            padding-bottom: 0px;
            margin-bottom: 0px;
        }

        .mt-0 {
            padding-top: 0px;
            margin-top: 0px;
        }

        .mb-5 {
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .mt-5 {
            padding-top: 5px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    @php
        $array_dias = [
            '1' => 'Lunes',
            '2' => 'Martes',
            '3' => 'Miércoles',
            '4' => 'Jueves',
            '5' => 'Viernes',
            '6' => 'Sábado',
            '0' => 'Domingo',
        ];
        $array_meses = [
            '01' => 'enero',
            '02' => 'febrero',
            '03' => 'marzo',
            '04' => 'abril',
            '05' => 'mayo',
            '06' => 'junio',
            '07' => 'julio',
            '08' => 'agosto',
            '09' => 'septiembre',
            '10' => 'octubre',
            '11' => 'noviembre',
            '12' => 'diciembre',
        ];
    @endphp
    <div class="encabezado">
        <div class="logo">
            <img src="{{ asset('imgs/' . App\RazonSocial::first()->logo) }}">
        </div>
        <h2 class="titulo">
            {{ App\RazonSocial::first()->nombre }}
        </h2>
        <h4 class="texto">RESULTADO DE LA OPERACIÓN</h4>
        <h4 class="fecha">(Del {{ date('d', strtotime($fecha_ini)) }} de
            {{ $array_meses[date('m', strtotime($fecha_ini))] }} {{ date('Y', strtotime($fecha_ini)) }}, al
            {{ date('d', strtotime($fecha_fin)) }} de {{ $array_meses[date('m', strtotime($fecha_fin))] }}
            {{ date('Y', strtotime($fecha_fin)) }})</h4>
        <h4 class="fecha">(Expresado en kilogramos)</h4>
    </div>
    <br>
    <br>
    <h4 class="seccion mb-0 mt-0">INGRESO POR VENTAS <span>{{ number_format($total_ventas, 2) }}</span></h4>
    <div class="info mb-0 mt-0">
        <h5 class="normal mb-5 mt-5">Venta de carne</h5>
    </div>
    <h4 class="seccion mb-0 mt-0">COSTOS POR COMPRAS <span>{{ number_format($total_compras, 2) }}</span></h4>
    <div class="info mb-0 mt-0">
        <h5 class="normal info_monto1  mb-5 mt-5">Compra de: <span class="bold">{{ $cantidad_compras }}</span></h5>
    </div>
    <h4 class="seccion  mb-5 mt-5">RESULTADO EN VENTAS <span
            class="total1">{{ number_format($resultado_ventas, 2) }}</span></h4>

    <h4 class="seccion mb-0">INGRESO OPERATIVOS <span>{{ number_format($total_ingresos, 2) }}</span></h4>
    <div class="info mb-0 mt-0">
        {!! $html_ingresos !!}
    </div>
    <h4 class="seccion mb-0">GASTOS OPERATIVOS <span>{{ number_format($total_egresos, 2) }}</span></h4>
    <div class="info mb-0 mt-0">
        {!! $html_egresos !!}
    </div>
    <h4 class="seccion mb-0">RESULTADO NETO DE LA OPERACIÓN <span
            class="total2">{{ number_format($resultado_neto, 2) }}</span></h4>

</body>

</html>
