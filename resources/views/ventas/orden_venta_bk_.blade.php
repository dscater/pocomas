<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Factura</title>
    <style type="text/css">
        * {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }

        @page {
            margin-left: 2.5cm;
            margin-top: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
        }

        body {
            position: relative;
        }

        .titulo {
            margin-right: auto;
            margin-left: auto;
            margin-bottom: auto;
            width: 300px;
        }

        .titulo p.emp {
            text-align: center;
            font-size: 0.95em;
            padding: 0;
            margin-bottom: -10px;
        }

        .titulo p.dir {
            text-align: center;
            font-size: 0.60em;
            padding: 0;
            margin-bottom: -10px;
        }

        .titulo p.activi {
            text-align: center;
            font-size: 0.60em;
            padding: 0;
        }

        .titulo_derecha {
            position: absolute;
            top: -70px;
            right: -55px;
            width: 180px;
        }

        .titulo_derecha h2 {
            text-align: center;
            font-size: 0.85em;
            color: #dc3545;
            font-family: Calibri, sans-serif;
            border: solid 1px #dc3545;
            background: #fdcbd0;
            margin-bottom: 2px;
        }

        .titulo_derecha .contenedor_info {
            padding-left: 5px;
            width: 100%;
            border: solid 1px #dc3545;
        }

        .titulo_derecha .contenedor_info p.info {
            font-size: 0.55em;
        }

        .logo {
            width: 130;
            height: 120px;
            position: absolute;
            top: -65px;
            left: -35px;
        }

        .datos_factura {
            font-size: 0.75em;
            width: 100%;
            margin-bottom: 10px;
            margin-top: 15px;
        }

        .datos_factura .c1 {
            width: 10%;
        }

        .datos_factura .c2 {
            width: 5%;
        }

        .factura {
            border-collapse: collapse;
            position: relative;
            width: 100%;
            font-size: 0.7em;
        }

        .factura thead tr {
            background: #dc3545;
            color: white;
        }

        .factura thead tr th {
            text-align: center;
        }

        .factura tbody tr td {
            text-align: center;
        }

        .factura tbody tr.total td:first-child {
            text-align: right;
            padding-right: 15px;
        }

        .factura tbody tr.total_final td:nth-child(4n),
        tr.total_final td:nth-child(5n) {
            background: #dc3545;
            color: white;
            font-weight: bold;
        }

        .factura tbody tr.total_literal td:nth-child(3n) {
            text-align: right;
            padding-right: 15px;
        }

        .factura tbody tr.total_literal td:nth-child(4n) {
            text-align: left;
            padding-left: 15px;
        }

        .codigos {
            margin-top: 35px;
            width: 70%;
        }

        .codigos tbody tr td {
            font-size: 0.7em;
        }

        .codigos tbody tr td.c1 {
            width: 15%;
        }

        .codigos tbody tr td.c2 {
            width: 65%;
        }

        .codigos tbody tr td.qr {
            width: 30%;
        }

        .qr {
            width: 120px;
            height: 120px;
        }

        .qr img {
            width: 100%;
            height: 100%;
        }

        .info1 {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 0.6em;
        }

        .info2 {
            text-align: center;
            font-weight: bold;
            font-size: 0.5em;
        }
    </style>
</head>

<body>
    @php
        $empresa = App\RazonSocial::first();
    @endphp
    <img src="{{ asset('imgs/' . $empresa->logo) }}" class="logo" alt="">
    <div class="titulo">
        <p class="emp">{{ $empresa->nombre }}</p>
        <p class="dir">{{ $empresa->ciudad }}-{{ $empresa->pais }}, {{ $empresa->dir }}</p>
        <p class="activi">ACTIVIDAD ECONÓMICA: {{ $empresa->actividad_economica }}</p>
    </div>
    <div class="titulo_derecha">
        <h2>Factura</h2>
        <div class="contenedor_info">
            <p class="info"><strong>NIT: </strong><span>{{ $empresa->nit }}</span></p>
            <p class="info"><strong>FACTURA N°: </strong><span>{{ $venta->id }}</span></p>
            <p class="info"><strong>AUTORIZACIÓN: </strong><span>{{ $empresa->nro_aut }}</span></p>
        </div>
    </div>
    <table class="datos_factura">
        <tbody>
            <tr>
                <td class="c1"><strong>A nombre de: </strong></td>
                <td>{{ $venta->cliente->nombre }}</td>
                <td class="c2"><strong>Fecha: </strong> </td>
                <td>{{ date('d-m-Y', strtotime($venta->fecha_venta)) }}</td>
            </tr>
            <tr>
                <td class="c2"><strong>NIT/C.I.: </strong></td>
                <td>{{ $venta->cliente->ci }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
    <table class="factura">
        <thead>
            <tr>
                <th>N°</th>
                <th>PRODUCTO</th>
                <th>C.U.</th>
                <th>CANTIDAD</th>
                <th>SUBTOTAL (Bs.)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $cont = 1;
            @endphp
            @foreach ($venta->detalle as $key => $value)
                <tr>
                    <td>{{ $cont++ }}</td>
                    <td>{{ $value->producto->nombre }}</td>
                    <td>{{ $value->monto }}</td>
                    <td>{{ $value->cantidad }}</td>
                    <td>{{ $value->sub_total }}</td>
                </tr>
            @endforeach
            <tr class="total_final">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="font-weight-bold">
                    TOTAL FINAL (Bs.)
                </td>
                <td>
                    {{ $venta->monto_total }}
                </td>
            </tr>
        </tbody>
    </table>
    {{-- <table class="codigos">
        <tbody>
            <tr>
                <td class="c1">
                    <strong>Código de control:</strong>
                </td>
                <td class="c2">
                    {{ $venta->codigo }}
                </td>
            </tr>
            <tr>
                <td class="c1">
                    <strong>Fecha límite de emisión:</strong>
                </td>
                <td class="c2">
                    {{ $venta->fecha_lim_emi }}
                </td>
            </tr>
        </tbody>
    </table> --}}
    {{-- <div class="qr">
        <img src="{{ asset('imgs/qr/qr.png') }}" alt="">
    </div> --}}
    <div class="info1">
        "ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS EL USO ILÍCITO DE ÉSTA SERA SANCIONADO A LEY"
    </div>
    <div class="info2">
        Ley Nº 453: El proveedor debe exhibir certificaciones de habilitación o documentos que acrediten las capacidades
        u ofertas de servicios.
    </div>
</body>

</html>
