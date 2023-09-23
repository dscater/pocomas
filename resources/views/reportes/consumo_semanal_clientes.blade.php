<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>ConsumoSemanalCliente</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 2cm;
            margin-bottom: 1cm;
            margin-left: 0.5cm;
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
        <h4 class="texto">VENTAS DIARIAS POR PRODUCTO</h4>
        <h4 class="fecha">Del {{ $array_dias[date('w', strtotime($fecha_ini))] }} {{ date('d', strtotime($fecha_ini)) }}
            de
            {{ $array_meses[date('m', strtotime($fecha_ini))] }} de {{ date('Y', strtotime($fecha_ini)) }}, al
            {{ $array_dias[date('w', strtotime($fecha_fin))] }} {{ date('d', strtotime($fecha_fin)) }} de
            {{ $array_meses[date('m', strtotime($fecha_fin))] }} de {{ date('Y', strtotime($fecha_fin)) }}</h4>
        <h4 class="fecha">(Expresado en kilogramos)</h4>
    </div>
    <table border="1">
        <thead>
            <tr>
                <th>CLIENTE</th>
                @for ($i = 1; $i <= $total_semanas; $i++)
                    <th>SEMANA {{ $i }}</th>
                @endfor
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_final = 0;
            @endphp
            @foreach ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nombre }}</td>
                    @php
                        $cantidad_total = 0;
                    @endphp
                    @for ($i = 1; $i <= $total_semanas; $i++)
                        <td class="centreado">{{ $consumo_semanal[$cliente->id][$i]['cantidad'] }}</td>
                        @php
                            $cantidad_total += (float) $consumo_semanal[$cliente->id][$i]['cantidad'];
                        @endphp
                    @endfor
                    <td class="centreado">{{ $cantidad_total }}</td>
                    @php
                        $total_final += $cantidad_total;
                    @endphp
                </tr>
            @endforeach
            <tr>
                <td colspan="{{ $total_semanas + 1 }}" class="derecha bold">TOTAL</td>
                <td class="centreado bold">{{ $total_final }} </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
