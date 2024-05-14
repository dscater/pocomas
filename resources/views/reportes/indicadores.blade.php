<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Indicadores</title>
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
            font-size: 0.89em;
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
        }

        table tbody tr td {
            padding: 3px;
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
            width: 90%;
        }

        .info_monto1 span {
            float: right;
        }

        .bold {
            font-weight: bold;
        }

        .normal {
            font-weight: normal;
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

        .contenedor_secciones {
            width: 100%;
            padding-left: 40px;
        }

        .table_info {
            width: 90%;
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
        <h4 class="texto">INDICADORES</h4>
        <h4 class="fecha">(Del {{ date('d', strtotime($fecha_ini)) }} de
            {{ $array_meses[date('m', strtotime($fecha_ini))] }} {{ date('Y', strtotime($fecha_ini)) }}, al
            {{ date('d', strtotime($fecha_fin)) }} de {{ $array_meses[date('m', strtotime($fecha_fin))] }}
            {{ date('Y', strtotime($fecha_fin)) }})</h4>
        {{-- <h4 class="fecha">(Expresado en kilogramos)</h4> --}}
    </div>
    <br>
    <br>
    <div class="contenedor_secciones">
        <h4 class="seccion mb-0">INFORMACIÓN DE COMPRA DE ANIMALES</span></h4>
        <h5 class="normal info_monto1  mb-5 mt-5">Cantidad <span class="bold">{{ $total_cantidad }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Total kilos <span class="bold">{{ $total_kilos }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Promedio animales <span
                class="bold">{{ $promedio_animales }}</span></h5>
        <h4 class="seccion mb-0">INFORMACIÓN DE VENTA DE CARNE</span></h4>
        <h5 class="normal info_monto1  mb-5 mt-5">Cantidad <span class="bold">{{ $ventas_cantidad }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Total kilos <span class="bold">{{ $ventas_kilos }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Promedio carne <span class="bold">{{ $promedio_carne }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Promedio de carne esperado <span
                class="bold">{{ $promedio_carne_esperado }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Diferencia promedio esperado <span
                class="bold">{{ $dif_promedio_esperado }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Promedio de biseras y menudos <span
                class="bold">{{ $promedio_bm }}</span></h5>
        <h4 class="seccion mb-0">PROMEDIO DE MENUDOS Y BISERAS</span></h4>
        {!! $html_menudos_biseras !!}
        <h4 class="seccion mb-0">INDICADORES DE INGRESOS Y GASTOS</span></h4>
        <h5 class="normal info_monto1  mb-5 mt-5">Ingresos operativos <span class="bold">{{ number_format($ingreso_operativo, 2) }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Gastos operativos <span class="bold">{{ number_format($egreso_operativo, 2) }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Ingresos Op - Gastos Op <span class="bold">{{ number_format($io_eo, 2) }}</span></h5>
        <h5 class="normal info_monto1  mb-5 mt-5">Gastos de personal <span class="bold">{{ number_format($gasto_personal, 2) }}</span></h5>
    </div>
</body>

</html>
