@extends('layouts.app')

@section('css')
    <style type="text/css">
        /* FACURA */
        .contenedor_factura {
            font-size: 0.9em;
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
            width: 100%;
        }

        .centreado {
            text-align: center;
        }

        .cobro table tr td {
            font-size: 0.8em;
        }

        .literal {
            font-size: 0.7em;
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

        .qr img {
            width: 160px;
            height: 160px;
        }

        .total {
            font-size: 0.9em !important;
        }

        .txt_lg {
            font-size: 1.1em !important;
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
                    <h1 class="m-0 text-dark">Cuentas por cobrar</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cuenta_cobrars.index') }}">Cuentas por cobrar</a>
                        </li>
                        <li class="breadcrumb-item active">Comprobante</li>
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
                            <h3 class="card-title">Cuenta por cobrar</h3>
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
                                        COMPROBANTE DE PAGO
                                    </div>
                                    @php
                                        $cuenta_pagos = App\CuentaPago::where('cuenta_id', $cuenta_cliente->id)
                                            ->orderBy('created_at', 'desc')
                                            ->take(3)
                                            ->get()
                                            ->reverse();
                                    @endphp
                                    <div class="elemento detalle izquierda">
                                        Fecha: {{ date('d/m/Y', strtotime($cuenta_cliente->created_at)) }} <br>
                                        Hora: {{ date('H:i:s', strtotime($cuenta_cliente->created_at)) }} <br>
                                        Cliente: {{ $cuenta_cliente->cliente->nombre }} <br>
                                        CI/NIT: {{ $cuenta_cliente->cliente->ci }} <br>
                                        Caja: {{ Auth::user()->name }} <br>
                                        Tipo de pago: {{ $cuenta_pagos[count($cuenta_pagos) - 1]->tipo_cobro }} <br>
                                    </div>
                                    <div class="elemento">
                                        DETALLE
                                    </div>
                                    <div class="cobro">
                                        <table>
                                            <tr class="punteado">
                                                <td class="centreado bold" width="50%">Fecha</td>
                                                <td class="centreado bold">Monto</td>
                                            </tr>
                                            @foreach ($cuenta_pagos as $key => $pago)
                                                @php
                                                    $punteado = '';
                                                    $texto = '';
                                                    $asterisco = '';
                                                    if ($key == 0) {
                                                        $texto = ' bold txt_lg';
                                                        $asterisco = '*';
                                                        $punteado = 'punteado';
                                                    }
                                                @endphp
                                                <tr class="{{ $punteado }}">
                                                    <td class="elemento centreado{{ $texto }}">
                                                        {{ $asterisco }}
                                                        {{ date('d/m/Y', strtotime($pago->fecha_pago)) }}
                                                    </td>
                                                    <td class="elemento centreado{{ $texto }}">
                                                        {{ $pago->monto }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                        <table>
                                            {{-- <tr>
                                                <td colspan="4" class="bold elemento">Monto Total:
                                                    {{ $cuenta_cliente->total_deuda }}</td>
                                            </tr> --}}
                                        </table>
                                    </div>
                                    <div class="izquierda literal">
                                        Son: {{ $literal }}
                                    </div>
                                    <div class="izquierda literal bold">
                                        Saldo Restante:
                                        {{ $cuenta_cliente->saldo }}
                                    </div>
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
                                    <a href="{{ route('cuenta_cobrars.index') }}"
                                        class="btn btn-default btn-flat btn-block"><i class="fa fa-list-alt"></i>
                                        Volver a cuentas por cobrar</a>
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
                        font-size: 0.95em;
                        width: 6.4cm !important;
                        padding-top: 15px;
                        padding-bottom: 15px;
                        font-family: 'Times New Roman', Times, serif;
                    }

                    .elemento {
                        text-align: center;
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
                        font-size: 0.65em;
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
                        width: 100%;
                    }

                    .centreado {
                        text-align: center;
                    }

                    .cobro table tr td {
                        font-size: 0.9em;
                    }

                    .literal {
                        font-size: 0.7em;
                    }

                    .cod_control,
                    .fecha_emision {
                        font-size: 0.9em;
                    }

                    .cobro table {
                        width: 97%;
                        border-collapse: collapse;
                    }

                    .cobro table tr.punteado td {
                        border-top: dashed 1px black;
                        border-bottom: dashed 1px black;
                    }

                    .qr img {
                        width: 160px;
                        height: 160px;
                    }

                    .total {
                        font-size: 0.9em !important;
                    }
                    .txt_lg {
                        font-size: 1.1em !important;
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
