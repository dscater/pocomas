<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inventario</title>
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
            margin-top: 0px;
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

        .producto {
            margin-bottom: -2px;
        }

        .producto tbody tr td {
            font-size: 0.8em;
            font-weight: bold;
            background: rgb(228, 228, 228);
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
        <h4 class="texto">KARDEX DE INVENTARIO</h4>
        <h4 class="fecha">Expedido: {{ date('Y-m-d') }}</h4>
    </div>
    @foreach ($productos as $producto)
        <br><br>
        <table class="producto" border="1">
            <tbody>
                <tr>
                    <td width="15%"><strong>Producto:</strong></td>
                    <td class="info2">{{ $producto->nombre }}</td>
                    <td width="18%"><strong>Existencia mínima:</strong> </td>
                    <td width="18%" class="info2">{{ $producto->stock_minimo }}</td>
                </tr>
            </tbody>
        </table>
        <table border="1">
            <thead>
                <tr>
                    <th colspan="4"></th>
                    <th colspan="3" class="fila1">
                        UNIDADES
                    </th>
                    <th></th>
                    <th colspan="3" class="fila1">
                        MONTOS
                    </th>
                </tr>
                <tr>
                    <th width="5%">Nro.</th>
                    <th>Fecha</th>
                    <th>Detalle</th>
                    <th>Precio Compra</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Saldo</th>
                    <th>C/U(Bs.)</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Saldo(Bs.)</th>
                </tr>
            </thead>
            <tbody>
                @if (count($array_kardex[$producto->id]) > 0 || $array_saldo_anterior[$producto->id]['sw'])
                    @php
                        $contador = 1;
                        $total = 0;
                    @endphp
                    @if ($array_saldo_anterior[$producto->id]['sw'])
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td></td>
                            <td>SALDO ANTERIOR</td>
                            <td></td>
                            <td></td>
                            <td>{{ $array_saldo_anterior[$producto->id]['saldo_anterior']['saldo_c'] }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($array_saldo_anterior[$producto->id]['saldo_anterior']['saldo_m'], 2, '.', ',') }}
                            </td>
                        </tr>
                    @endif
                    @foreach ($array_kardex[$producto->id] as $value)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha'])) }}</td>
                            <td>{{ $value['detalle'] }}</td>
                            <td>{{ $value['precio'] }}</td>
                            <td>{{ $value['ingreso_c'] }}</td>
                            <td>{{ $value['salida_c'] }}</td>
                            <td>{{ $value['saldo_c'] }}</td>
                            <td>{{ number_format($value['cu'], 2, '.', ',') }}</td>
                            <td>{{ $value['ingreso_m'] }}</td>
                            <td>{{ $value['salida_m'] }}</td>
                            <td>{{ number_format($value['saldo_m'], 2, '.', ',') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">NO SE ENCONTRARON REGISTROS</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach

</body>

</html>
