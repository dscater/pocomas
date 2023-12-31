@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Lotes de Productos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Ingresos Productos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- <h3 class="card-title"></h3> --}}
                            <a href="{{ route('lote_productos.create') }}" class="btn btn-info"><i class="fa fa-plus"></i>
                                Nuevo</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha Ingreso</th>
                                        <th>Nro. Lote</th>
                                        <th>Proveedor</th>
                                        <th>Producto</th>
                                        <th>Precio Compra</th>
                                        <th>Kilos de Cerdo</th>
                                        <th>Precio Total</th>
                                        <th>Cantidad de Cerdos</th>
                                        <th>Descripción</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($ingreso_productos as $ingreso_producto)
                                        <tr>
                                            <td>{{ $ingreso_producto->fecha_ingreso }}</td>
                                            <td>{{ $ingreso_producto->nro_lote }}</td>
                                            <td>{{ $ingreso_producto->proveedor->razon_social }}</td>
                                            <td>{{ $ingreso_producto->producto->nombre }}
                                            </td>
                                            <td>{{ $ingreso_producto->precio_compra }}</td>
                                            <td>{{ $ingreso_producto->total_kilos }}</td>
                                            <td>{{ $ingreso_producto->precio_total }}</td>
                                            <td>{{ $ingreso_producto->total_cantidad }}</td>
                                            <td>{{ $ingreso_producto->descripcion }}</td>
                                            <td class="btns-opciones">
                                                @if (Auth::user()->tipo == 'ADMINISTRADOR' && !$ingreso_producto->existe_ventas && !$ingreso_producto->existe_pagos)
                                                    <a href="{{ route('lote_productos.edit', $ingreso_producto->id) }}"
                                                        class="modificar"><i class="fa fa-edit" data-toggle="tooltip"
                                                            data-placement="left" title="Modificar"></i></a>

                                                    <a href="#"
                                                        data-url="{{ route('lote_productos.destroy', $ingreso_producto->id) }}"
                                                        data-toggle="modal" data-target="#modal-eliminar"
                                                        class="eliminar"><i class="fa fa-trash" data-toggle="tooltip"
                                                            data-placement="left" title="Eliminar"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

    @include('modal.eliminar')

@section('scripts')
    <script>
        @if (session('bien'))
            mensajeNotificacion('{{ session('bien') }}', 'success');
        @endif

        @if (session('info'))
            mensajeNotificacion('{{ session('info') }}', 'info');
        @endif

        @if (session('error'))
            mensajeNotificacion('{{ session('error') }}', 'error');
        @endif


        $('table.data-table').DataTable({
            order: [
                [0, 'desc']
            ],
            columns: [{
                    width: "5%"
                },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                {
                    width: "10%"
                },
            ],
            scrollCollapse: true,
            language: lenguaje,
            pageLength: 25
        });


        // ELIMINAR
        $(document).on('click', 'table tbody tr td.btns-opciones a.eliminar', function(e) {
            e.preventDefault();
            let ingreso_productos = $(this).parents('tr').children('td').eq(1).text();
            $('#mensajeEliminar').html(
                `¿Está seguro(a) de eliminar lote nro.: <b>${ingreso_productos}</b>?`);
            let url = $(this).attr('data-url');
            console.log($(this).attr('data-url'));
            $('#formEliminar').prop('action', url);
        });

        $('#btnEliminar').click(function() {
            $('#formEliminar').submit();
        });
    </script>
@endsection
@endsection
