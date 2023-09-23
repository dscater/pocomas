@extends('layouts.app')

@section('css')
    <style type="text/css">
        .contenedor_factura .card {
            overflow: auto;
        }

        .factura {
            min-width: 800px;
            overflow: auto;
        }

        .cont_header {
            position: relative;
            justify-content: space-between;
            flex-direction: column;
        }

        .titulo p.emp {
            text-align: center;
            font-size: 0.95em;
            padding: 0;
            margin-bottom: 2px;
        }

        .titulo p.dir {
            text-align: center;
            font-size: 0.9em;
            padding: 0;
            margin-bottom: 2px;
        }

        .titulo p.activi {
            text-align: center;
            font-size: 0.9em;
            padding: 0;
        }

        .titulo_derecha {
            position: absolute;
            top: 0px;
            right: 0px;
            width: 250px;
        }

        .titulo_derecha h2 {
            text-align: center;
            font-size: 0.95em;
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
            font-size: 0.9em;
        }

        .logo {
            width: 200px;
            top: 0px;
            left: 0px;
        }

        .logo img {
            width: 100%;
        }

        .datos_factura {
            font-size: 1em;
            width: 100%;
            margin-bottom: 10px;
            margin-top: 15px;
        }

        .datos_factura .c1 {
            width: 10%;
            text-align: left;
        }

        .datos_factura .c2 {
            width: 5%;
            text-align: left;
        }

        .factura {
            border-collapse: collapse;
            position: relative;
            width: 100%;
            font-size: 0.9em;
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
            font-size: 1em;
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
            width: 100%;
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 0.8em;
        }

        .info2 {
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 0.7em;
        }
    </style>
@endsection

@section('content')
    @php
        $empresa = App\RazonSocial::first();
    @endphp
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ventas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                        <li class="breadcrumb-item active">Factura</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 contenedor_factura">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Factura</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body factura">
                            <div class="row cont_header">
                                <div class="logo">
                                    <img src="{{ asset('imgs/' . $empresa->logo) }}"alt="">
                                </div>
                                <div class="titulo">
                                    <p class="emp text-md font-weight-bold">{{ $empresa->nombre }}</p>
                                    <p class="dir">{{ $empresa->ciudad }}-{{ $empresa->pais }}, {{ $empresa->dir }}</p>
                                    <p class="activi">ACTIVIDAD ECONÓMICA: {{ $empresa->actividad_economica }}</p>
                                </div>
                                <div class="titulo_derecha">
                                    <h2 class="font-weight-bold text-md">Factura</h2>
                                    <div class="contenedor_info">
                                        <p class="info"><strong>NIT: </strong><span>{{ $empresa->nit }}</span></p>
                                        <p class="info"><strong>FACTURA N°: </strong><span>{{ $venta->id }}</span></p>
                                        <p class="info"><strong>AUTORIZACIÓN:
                                            </strong><span>{{ $empresa->nro_aut }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <table class="datos_factura">
                                <tbody>
                                    <tr>
                                        <td class="c1"><strong>A nombre de: </strong></td>
                                        <td style="text-align:left;">{{ $venta->cliente->nombre }}</td>
                                        <td class="c2"><strong>Fecha: </strong> </td>
                                        <td style="text-align:left;">{{ date('d-m-Y', strtotime($venta->fecha_venta)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="c2"><strong>NIT/C.I.: </strong></td>
                                        <td style="text-align:left;">{{ $venta->cliente->ci }}</td>
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
                            <div class="row">
                                <div class="info1">
                                    "ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS EL USO ILÍCITO DE ÉSTA SERA SANCIONADO A
                                    LEY"
                                </div>
                                <div class="info2">
                                    Ley Nº 453: El proveedor debe exhibir certificaciones de habilitación o documentos que
                                    acrediten las capacidades
                                    u ofertas de servicios.
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('ventas.orden_venta', $venta->id) }}" target="_blank"
                                        class="btn btn-danger" data-toggle="tooltip" title="Factura"><i
                                            class="fa fa-file-pdf"></i> Exportar</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
    </section>
@endsection

@section('scripts')
@endsection
