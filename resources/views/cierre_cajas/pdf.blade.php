<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
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
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }

        table thead tr th,
        tbody tr td {
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
            font-size: 0.55em;
        }

        table tbody tr td.franco {
            background: red;
            color: white;
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

        .p_cump {
            color: red;
            font-size: 1.2em;
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

        .centreado {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="encabezado">
        <div class="logo">
            <img src="{{ asset('imgs/' . App\RazonSocial::first()->logo) }}">
        </div>
        <h2 class="titulo">
            {{ App\RazonSocial::first()->nombre }}
        </h2>
        <h4 class="texto">CIERRE DE CAJA</h4>
        <h4 class="fecha">Expedido: {{ date('Y-m-d') }}</h4>
        <h4 class="fecha">{{ $cierre_caja->caja->nombre }}</h4>
    </div>
    <h4>Listado de comprobantes</h4>
    <table border="1">
        <thead>
            <tr>
                <th width="5%">Nº</th>
                <th>FECHA</th>
                <th>HORA</th>
                <th>CONCEPTO (DETALLE)</th>
                <th>INGRESO/EGRESO</th>
                <th>MONTO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $cont = 1;
                $total_ingresos = 0;
                $total_egresos = 0;
                $total_realizado = 0;
            @endphp
            @foreach ($ingreso_cajas as $ic)
                <tr>
                    <td>{{ $cont++ }}</td>
                    <td>{{ $ic->fecha }}</td>
                    <td>{{ $ic->hora }}</td>
                    <td>{{ $ic->concepto ? $ic->concepto->nombre . ' (' . $ic->tipo . ')' : 'S/C - (' . $ic->tipo . ')' }}</td>
                    <td>{{ $ic->tipo_movimiento }}</td>
                    <td>{{ $ic->monto_total }}</td>
                </tr>
                @php
                    if ($ic->tipo_movimiento == 'INGRESO') {
                        $total_ingresos += (float) $ic->monto_total;
                    } else {
                        $total_egresos += (float) $ic->monto_total;
                    }
                @endphp
            @endforeach
            @php
                $total_realizado = (float) $total_ingresos - (float) $total_egresos;
            @endphp
        </tbody>
    </table>
    <h4>Totales</h4>
    <table border="1">
        <thead>
            <tr>
                <th>TOTAL INGRESOS</th>
                <th>TOTAL EGRESOS</th>
                <th>TOTAL REALIZADO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="centreado">{{ $total_ingresos }}</td>
                <td class="centreado">{{ $total_egresos }}</td>
                <td class="centreado">{{ $total_realizado }}</td>
            </tr>
        </tbody>
    </table>

    <p><strong>Usuario: </strong>{{ $cierre_caja->user->name }}</p>
    <p><strong>Fecha cierre: </strong>{{ $cierre_caja->fecha_registro }}</p>
</body>

</html>
