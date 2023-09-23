<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>VentasDiariasCajas</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 2cm;
            margin-bottom: 1cm;
            margin-left: 1.5cm;
            margin-right: 1cm;
            border: 5px solid blue;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: auto;
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

        table thead {
            background: rgb(236, 236, 236)
        }

        table thead tr th {
            padding: 3px;
            font-size: 0.7em;
        }

        table tbody tr td {
            padding: 3px;
            font-size: 0.85em;
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

        .lista_productos{
            list-style: none;
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
        <h4 class="texto">VENTA DIARIA CAJAS</h4>
        <h4 class="fecha">{{ $array_dias[date('w', strtotime($fecha))] }}, {{ date('d', strtotime($fecha)) }} de
            {{ $array_meses[date('m', strtotime($fecha))] }} de {{ date('Y', strtotime($fecha)) }}</h4>
        <h4 class="fecha">(Expresado en bolivianos)</h4>
    </div>

    <table>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td class="bold">Bolivianos</td>
            </tr>
            <tr>
                <td colspan="2" class="bold">Saldo inicial</td>
                <td>{{ $inicio_caja->monto_inicial }}</td>
            </tr>
            <tr>
                <td colspan="2">Traspaso de caja principal</td>
                <td>{{ $ingreso_caja_central }}</td>
            </tr>
            @foreach($ventas as $venta)
            <tr>
                <td>{{$venta->cliente->nombre}}</td>
                <td>
                    <ul class="lista_productos">
                        @foreach($venta->detalle as $detalle)
                        <li>{{$detalle->producto->nombre}}</li>
                        @endforeach
                    </ul>
                </td>
                <td>{{ $venta->monto_total }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2">Gastos</td>
                <td>{{ $gastos }}</td>
            </tr>
            <tr>
                <td colspan="2">Traspaso a caja central</td>
                <td>{{ $traspasos_caja_central }}</td>
            </tr>
            <tr>
                <td colspan="2" class="bold">Total efectivo</td>
                <td>{{ $total_efectivo }}</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
