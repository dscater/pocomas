@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cajas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Cajas</li>
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
                            <a href="{{ route('cajas.create') }}" class="btn btn-info"><i class="fa fa-plus"></i> Nuevo</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Saldo Actual</th>
                                        <th>Descripción</th>
                                        <th>Fecha Registro</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($cajas as $caja)
                                        <tr>
                                            <td>{{ $cont++ }}</td>
                                            <td>{{ $caja->codigo }}</td>
                                            <td>{{ $caja->nombre }}</td>
                                            <td>{{ number_format($caja->saldo_actual, 2) }}</td>
                                            <td>{{ $caja->descripcion }}</td>
                                            <td>{{ $caja->fecha_registro }}</td>
                                            <td class="btns-opciones">
                                                <a href="{{ route('ingreso_cajas.index', $caja->id) }}" class="evaluar"><i
                                                        class="fa fa-cash-register" data-toggle="tooltip"
                                                        data-placement="left" title="Ingresos y Egresos"></i></a>

                                                <a href="{{ route('cajas.edit', $caja->id) }}" class="modificar"><i
                                                        class="fa fa-edit" data-toggle="tooltip" data-placement="left"
                                                        title="Modificar"></i></a>
                                                @if ((float) $caja->saldo_actual > 0)
                                                    <a href="#" data-url="{{ route('cierre_cajas.store') }}"
                                                        data-id="{{ $caja->id }}"
                                                        data-saldo="{{ $caja->saldo_actual }}" data-toggle="modal"
                                                        data-target="#modal_cerrar_caja"
                                                        class="ir-evaluacion cerrar_caja"><i class="fa fa-times"
                                                            data-toggle="tooltip" data-placement="left"
                                                            title="Cerrar Caja"></i></a>
                                                @endif

                                                <a href="#" data-url="{{ route('cajas.destroy', $caja->id) }}"
                                                    data-toggle="modal" data-target="#modal-eliminar" class="eliminar"><i
                                                        class="fa fa-trash" data-toggle="tooltip" data-placement="left"
                                                        title="Eliminar"></i></a>
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
    @include('modal.confirmar_cierre')

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
            let caja = $(this).parents('tr').children('td').eq(2).text();
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar el registro <b>${caja}</b>?`);
            let url = $(this).attr('data-url');
            console.log($(this).attr('data-url'));
            $('#formEliminar').prop('action', url);
        });

        $('#btnEliminar').click(function() {
            $('#formEliminar').submit();
        });

        // CERRAR CAJA
        $(document).on('click', 'table tbody tr td.btns-opciones a.cerrar_caja', function(e) {
            e.preventDefault();
            let caja = $(this).parents('tr').children('td').eq(2).text();
            let caja_id = $(this).attr('data-id');
            let saldo = $(this).attr('data-saldo');
            $('#mensajeCerrarCaja').html(
                `¿Está seguro(a) de cerrar la caja <b>${caja}</b>?<br>Saldo actual: ${saldo}<input type="hidden" name="caja_id" value="${caja_id}"/><input type="hidden" name="monto_total" value="${saldo}"/>`
            );
            let url = $(this).attr('data-url');
            $('#formCerrarCaja').prop('action', url);
        });

        $('#btnCerrarCaja').click(function() {
            $('#formCerrarCaja').submit();
        });
    </script>
@endsection
@endsection
