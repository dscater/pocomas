@extends('layouts.app')

@section('css')
    <style type="text/css">
        /* FACURA */
        .contenedor_factura {
            font-size: 9pt;
            width: 6.4cm !important;
            padding-top: 15px;
            padding-bottom: 15px;
            font-family: 'Times New Roman', Times, serif;
        }

        .col-md-4.contenedor_factura {
            margin-left: auto;
            margin-right: auto;
        }

        .elemento {
            text-align: center;
            font-size: 0.8em;
        }

        .elemento.logo img {
            width: 60%;
        }

        .separador {
            padding: 0px;
            margin: 0px;
        }

        .fono,
        .lp {
            font-size: 0.7em;
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
            font-size: 0.68em;
        }

        .info1 {
            text-align: center;
            font-weight: bold;
            font-size: 0.7em;
        }

        .info2 {
            text-align: center;
            font-weight: bold;
            font-size: 0.67em;
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
            width: 98%;
        }

        .centreado {
            text-align: center;
        }

        .cobro table tr td {
            font-size: 0.8em;
            word-break: break-all;
        }

        .cobro table tr td:nth-child(3),
        .cobro table tr td:nth-child(41) {
            word-break: normal;
        }

        .literal {
            font-size: 0.85em;
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

        .cobro table tr.punteado_top td {
            border-top: dashed 1px black;
        }

        .qr img {
            width: 160px;
            height: 160px;
        }

        .total {
            font-size: 0.9em !important;
        }

        .pr-10 {
            padding-right: 10px;
            font-size: 9pt !important;
        }

        @media (max-width:800px) {
            .col-md-4.contenedor_factura {
                margin-left: 0px !important;
                margin-right: 0px !important;
                width: 100% !important;
            }
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
                <div class="col-md-4 contenedor_factura">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Orden de venta</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body factura">
                            <div id="principal">
                                <div class="contenedor_factura ml-auto mr-auto" id="contenedor_imprimir">
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
                                        CI: {{ $venta->factura->nit }} <br>
                                        Caja: {{ $venta->user->name }} <br>
                                        Tipo de Pago: {{ $venta->tipo_venta }} <br>
                                        @if ($venta->tipo_venta == 'ANTICIPOS')
                                            Anticipo: {{ $venta->anticipo }} <br>
                                            Saldo: {{ $venta->saldo }} <br>
                                        @endif
                                    </div>
                                    <div class="elemento">
                                        DETALLE
                                    </div>
                                    <div class="cobro">
                                        <table>
                                            <tr class="punteado">
                                                <td class="centreado" width="40px">CANT.</td>
                                                <td class="centreado">PROD.</td>
                                                <td class="centreado">CU</td>
                                                <td class="centreado">SUBTOTAL</td>
                                            </tr>
                                            @foreach ($venta->detalle as $value)
                                                <tr>
                                                    <td class="centreado">{{ $value->cantidad_kilos }} Kg
                                                        ({{ $value->cantidad }} C)</td>
                                                    <td class="izquierda">{{ $value->producto->nombre }}</td>
                                                    <td class="centreado">{{ $value->monto }}</td>
                                                    <td class="centreado">{{ $value->sub_total }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                        <table>
                                            <tr class="punteado_top">
                                                <td colspan="4" class="bold elemento derecha pr-10">Total
                                                    {{ $venta->monto_total }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="izquierda literal">
                                        Son: {{ $literal }}
                                    </div>
                                    @if ($venta->tipo_venta != 'POR COBRAR')
                                        <div class="izquierda literal">
                                            <span class="bold">Recibido:</span> {{ $venta->monto_recibido }}
                                        </div>
                                    @endif
                                    @if ($venta->tipo_venta == 'AL CONTADO')
                                        <div class="izquierda literal">
                                            <span class="bold">Cambio:</span> {{ $venta->monto_cambio }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-danger btn-block btn-flat" id="btnImprimir"><i
                                            class="fa fa-print"></i>
                                        Imprimir</button>
                                    {{-- <a href="{{ route('ventas.orden_venta', $venta->id) }}" target="_blank"
                                        class="btn btn-danger btn-flat btn-block"><i class="fa fa-file-pdf"></i>
                                        Exportar</a> --}}
                                    <a href="{{ route('ventas.index') }}" class="btn btn-default btn-flat btn-block"><i
                                            class="fa fa-list-alt"></i>
                                        Volver a ventas</a>
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
    <script>
        let btnImprimir = $("#btnImprimir");
        @if (isset($_GET['imprime']) == true)
            imrpimirContenedor();
        @endif

        btnImprimir.click(imrpimirContenedor);

        function imrpimirContenedor() {
            var divContents = document.getElementById("principal").innerHTML;
            var a = window.open('', '');
            a.document.write('<html>');
            a.document.write('<head>');
            a.document.write(
                `
                <style>
                    @page { margin: 0; }

                    #principal{
                        width: 6.4cm !important;
                    }

                    #contenedor_imprimir {
                        font-size: 9pt;
                        width: 6.4cm !important;
                        padding-top: 15px;
                        padding-bottom: 15px;
                        font-family: 'Times New Roman', Times, serif;
                    }

                    .elemento {
                        text-align: center;
                        font-size: 0.9em;
                    }

                    .elemento.logo img {
                        width: 60%;
                    }

                    .separador {
                        padding: 0px;
                        margin: 0px;
                    }

                    .fono,
                    .lp {
                        font-size: 0.8em;
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
                        font-size: 0.7em;
                    }

                    .info1 {
                        text-align: center;
                        font-weight: bold;
                        font-size: 0.7em;
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
                        width: 97%;
                    }

                    .centreado {
                        text-align: center;
                    }

                    .cobro table tr td {
                        font-size: 0.7em;
                        word-break: break-all;
                    }
                    .cobro table tr td:nth-child(3) {
                        word-break: normal;
                    }

                    .literal {
                        font-size: 0.85em;
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

                    .cobro table tr.punteado_top td {
                        border-top: dashed 1px black;
                    }

                    .qr img {
                        width: 160px;
                        height: 160px;
                    }

                    .total {
                        font-size: 0.9em !important;
                    }

                    .pr-10 {
                        padding-right: 10px;
                        font-size: 9pt !important;
                    }
                </style>
                `
            );
            a.document.write('</head>');
            a.document.write('<body >');
            a.document.write(divContents);
            a.document.write('</body></html>');
            a.document.close();
            a.print();
        }
    </script>
@endsection
