@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Caja Central</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Caja Central</li>
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
                        <div class="card-header bg-dark">
                            <div class="row">
                                <div class="col-md-3 ml-auto">
                                    <div class="alert alert-danger text-lg font-weight-bold mt-2 text-center">TOTAL:
                                        {{ $saldo_central }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-primary text-lg font-weight-bold mt-2 text-center">BANCO:
                                        {{ $saldo_banco }}</div>
                                </div>
                                <div class="col-md-3 mr-auto">
                                    <div class="alert bg-orange text-lg font-weight-bold mt-2 text-center">CAJA:
                                        {{ $saldo_caja }}</div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-4 mt-0">
                                <div class="col-md-12">
                                    <a href="{{ route('caja_centrals.create') }}" class="btn btn-info"><i
                                            class="fa fa-plus"></i>
                                        Nuevo registro</a>
                                </div>
                            </div>
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Concepto</th>
                                        <th>Descripción</th>
                                        <th>Tipo de Movimiento</th>
                                        <th>Transacción</th>
                                        <th>Fecha de Registro</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($caja_centrals as $caja_central)
                                        <tr>
                                            <td>{{ date('d/m/Y', strtotime($caja_central->fecha)) }}</td>
                                            <td>{{ $caja_central->monto }}</td>
                                            <td>{{ $caja_central->concepto ? $caja_central->concepto->nombre : ($caja_central->ingreso_producto ? 'LOTE' : '-') }}
                                            </td>
                                            <td>{{ $caja_central->descripcion }}</td>
                                            <td>{{ $caja_central->tipo }}</td>
                                            <td>{{ $caja_central->tipo_transaccion }}</td>
                                            <td>{{ date('d/m/Y', strtotime($caja_central->fecha_registro)) }}</td>
                                            <td class="btns-opciones">
                                                @if ($caja_central->modificable)
                                                    <a href="{{ route('caja_centrals.edit', $caja_central->id) }}"
                                                        class="modificar"><i class="fa fa-edit" data-toggle="tooltip"
                                                            data-placement="left" title="Modificar"></i></a>
                                                @endif
                                                @if (Auth::user()->tipo == 'ADMINISTRADOR' && $caja_central->modificable)
                                                    <a href="#"
                                                        data-url="{{ route('caja_centrals.destroy', $caja_central->id) }}"
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
            let caja_central = $(this).parents('tr').children('td').eq(2).text();
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar el registro <b>${caja_central}</b>?`);
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
