@extends('layouts.app')

@section('css')
    <style>
        .limite {
            background: rgb(245, 114, 114);
        }

        .limite:hover {
            background: rgb(245, 114, 114) !important;
        }
    </style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Productos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Productos</li>
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
                            <a href="{{ route('productos.create') }}" class="btn btn-info"><i class="fa fa-plus"></i>
                                Nuevo</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>Foto</th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Abrev.</th>
                                        <th>Descripción</th>
                                        <th>Stock Mínimo</th>
                                        <th>Stock Actual Kilos</th>
                                        <th>Stock Actual Cantidad</th>
                                        <th>Precio de Venta</th>
                                        <th>Tipo de Venta</th>
                                        <th>Fecha Registro</th>
                                        <th>Estado</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($productos as $producto)
                                        @php
                                            $limite = '';
                                            if ($producto->stock_actual <= $producto->stock_minimo) {
                                                $limite = 'limite';
                                            }
                                        @endphp
                                        <tr class="{{ $limite }}">
                                            <td>{{ $cont++ }}</td>
                                            <td class="text-center"><img
                                                    src="{{ asset('imgs/productos/' . $producto->foto) }}" alt="Imagen"
                                                    style="height:50px;width:50px;"></td>
                                            <td>{{ $producto->codigo }}</td>
                                            <td>{{ $producto->nombre }}</td>
                                            <td>{{ $producto->abrev }}</td>
                                            <td>{{ $producto->descripcion }}</td>
                                            <td>{{ $producto->stock_minimo }}</td>
                                            <td>{{ $producto->stock_actual }}</td>
                                            <td>{{ $producto->stock_actual_cantidad }}</td>
                                            <td>{{ $producto->precio }}</td>
                                            <td>{{ $producto->tipo_venta }}</td>
                                            <td>{{ $producto->fecha_registro }}</td>
                                            <td>{{ $producto->estado }}</td>
                                            <td class="btns-opciones">
                                                <a href="{{ route('productos.edit', $producto->id) }}" class="modificar"><i
                                                        class="fa fa-edit" data-toggle="tooltip" data-placement="left"
                                                        title="Modificar"></i></a>
                                                @if (Auth::user()->tipo == 'ADMINISTRADOR')
                                                    <a href="#"
                                                        data-url="{{ route('productos.destroy', $producto->id) }}"
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
            columns: [{
                    width: "5%"
                },
                null,
                null,
                null,
                null,
                null,
                {
                    width: "5%"
                },
                {
                    width: "5%"
                },
                {
                    width: "5%"
                },
                {
                    width: "5%"
                },
                {
                    width: "5%"
                },
                {
                    width: "5%"
                },
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
            let productos = $(this).parents('tr').children('td').eq(3).text();
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar el registro <b>${productos}</b>?`);
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
