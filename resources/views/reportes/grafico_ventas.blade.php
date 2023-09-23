@extends('layouts.app')

@section('css')
    <style>
        .boton_reporte {
            width: 100% !important;
            margin-left: auto;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .boton_reporte a {
            width: 100%;
        }

    </style>
@endsection

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-white">Reportes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-white">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Reportes</a></li>
                        <li class="breadcrumb-item active">Gráfico Contratos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content" id="contenedorReportes">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h3>Cantidad de Ventas</h3>
                        <div class="row" id="contFiltros">
                            <div class="col-md-8 ml-auto mr-auto">
                                <select class="form-control" name="filtro" id="filtro">
                                    <option value="todos">Todos</option>
                                    <option value="caja">Por Caja</option>
                                    <option value="producto">Por Producto</option>
                                    <option value="fecha">Por Fecha</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha inicio:</label>
                                    <input type="date" name="fecha_ini" id="fecha_ini" value="{{ date('Y-m-d') }}"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha fin:</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ date('Y-m-d') }}"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Seleccione:</label>
                                    {{ Form::select('caja', $array_cajas, null, ['class' => 'form-control', 'id' => 'caja']) }}
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Seleccione:</label>
                                    {{ Form::select('producto', $array_productos, null, ['class' => 'form-control', 'id' => 'producto']) }}
                                </div>
                            </div>
                        </div>
                        <div id="contenedorGrafico"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="urlInfo" value="{{ route('reportes.info_ventas') }}">

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            ventas();
            cargaGrafico();
            $('#contFiltros').on('change', 'select', function() {
                cargaGrafico();
            });

            $('#contFiltros').on('change keyup', 'input', function() {
                cargaGrafico();
            });
        });

        function cargaGrafico() {
            $.ajax({
                type: "GET",
                url: $('#urlInfo').val(),
                data: {
                    filtro: $('#filtro').val(),
                    fecha_ini: $('#fecha_ini').val(),
                    fecha_fin: $('#fecha_fin').val(),
                    caja: $('#caja').val(),
                    producto: $('#producto').val(),
                },
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('contenedorGrafico', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'CANTIDAD DE VENTAS'
                        },
                        subtitle: {
                            text: response.fecha
                        },
                        xAxis: {
                            categories: response.categorias,
                            crosshair: true,
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Cantidad Ventas'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.0f} venta(s)</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: response.series,
                        lang: {
                            downloadCSV: 'Descargar CSV',
                            downloadJPEG: 'Descargar imagen JPEG',
                            downloadPDF: 'Descargar Documento PDF',
                            downloadPNG: 'Descargar imagen PNG',
                            downloadSVG: 'Descargar vector de imagen SVG ',
                            downloadXLS: 'Descargar XLS',
                            viewFullscreen: 'Ver pantalla completa',
                            printChart: 'Imprimir',
                            exitFullscreen: 'Salir de pantalla completa'
                        }
                    });

                }
            });
        }

        function ventas() {
            var caja = $('#contFiltros #caja').parents('.form-group');
            var producto = $('#contFiltros #producto').parents('.form-group');
            var fecha_ini = $('#contFiltros #fecha_ini').parents('.form-group');
            var fecha_fin = $('#contFiltros #fecha_fin').parents('.form-group');

            fecha_ini.hide();
            fecha_fin.hide();
            caja.hide();
            producto.hide();
            $('#contFiltros select#filtro').change(function() {
                let filtro = $(this).val();
                switch (filtro) {
                    case 'todos':
                        caja.hide();
                        producto.hide();
                        fecha_ini.hide();
                        fecha_fin.hide();
                        break;
                    case 'fecha':
                        fecha_ini.show();
                        fecha_fin.show();
                        caja.hide();
                        producto.hide();
                        break;
                    case 'caja':
                        caja.show();
                        producto.hide();
                        fecha_ini.hide();
                        fecha_fin.hide();
                        break;
                    case 'producto':
                        caja.hide();
                        producto.show();
                        fecha_ini.hide();
                        fecha_fin.hide();
                        break;
                }
            });
        }

    </script>
@endsection
