@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cuentas por Cobrar</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Cuentas por Cobrar</li>
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
                            <a href="{{ route('cuenta_cobrars.create') }}" class="btn btn-info"><i class="fa fa-plus"></i>
                                Registrar Pagos</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>Cliente</th>
                                        <th>Total Acumulado</th>
                                        <th>Saldo Restante</th>
                                        <th>Último Pago</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($cuenta_cobrars as $cuenta_cobrar)
                                        <tr>
                                            <td>{{ $cont++ }}</td>
                                            <td>{{ $cuenta_cobrar->cliente->nombre }}</td>
                                            <td>{{ $cuenta_cobrar->total_deuda }}</td>
                                            <td>{{ $cuenta_cobrar->saldo }}</td>
                                            <td>{{ $cuenta_cobrar->ultimo_pago ? $cuenta_cobrar->ultimo_pago->monto : '0.00' }}
                                            </td>
                                            <td>
                                                @if ($cuenta_cobrar->estado == 'PENDIENTE')
                                                    <span
                                                        class="badge badge-danger text-xs">{{ $cuenta_cobrar->estado }}</span>
                                                @else
                                                    <span
                                                        class="badge badge-success text-xs">{{ $cuenta_cobrar->estado }}</span>
                                                @endif
                                            </td>
                                            <td class="btns-opciones">
                                                <a href="{{ route('cuenta_cobrars.pagos', $cuenta_cobrar->id) }}"
                                                    class="evaluar" data-toggle="tooltip" title="Ver Pagos"><i
                                                        class="fa fa-list"></i></a>
                                                @if (count($cuenta_cobrar->cuenta_pagos) > 0)
                                                    <a href="{{ route('cuenta_cobrars.comprobante', $cuenta_cobrar->id) }}"
                                                        class="ir-evaluacion" data-toggle="tooltip" title="Comprobante"><i
                                                            class="fa fa-file-pdf"></i></a>
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
            ],
            scrollCollapse: true,
            language: lenguaje,
            pageLength: 25
        });


        // ELIMINAR
        $(document).on('click', 'table tbody tr td.btns-opciones a.eliminar', function(e) {
            e.preventDefault();
            let cuenta_cobrar = $(this).parents('tr').children('td').eq(2).text();
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar el registro <b>${cuenta_cobrar}</b>?`);
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
