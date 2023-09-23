@extends('layouts.app')

@section('css')
    <style>
        .completo,
        .completo:hover {
            background: rgb(132, 219, 139) !important;
        }

        .saldo,
        .saldo:hover {
            background: rgb(245, 114, 114) !important;
        }
    </style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cuentas por pagar</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Cuentas por pagar</li>
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
                            <a href="{{ route('cuenta_pagars.create') }}" class="btn btn-info"><i class="fa fa-plus"></i>
                                Nuevo registro</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Nro. de Lote</th>
                                        <th>Proveedor</th>
                                        <th>Monto Total</th>
                                        <th>Saldo</th>
                                        <th>Descripción</th>
                                        <th>Fecha de Registro</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($cuenta_pagars as $cuenta_pagar)
                                        @php
                                            $bg = 'saldo';
                                            if ($cuenta_pagar->saldo == 0) {
                                                $bg = 'completo';
                                            }
                                        @endphp
                                        <tr class="{{ $bg }}">
                                            <td>{{ $cuenta_pagar->ingreso_producto->nro_lote }}</td>
                                            <td>{{ $cuenta_pagar->proveedor->razon_social }}</td>
                                            <td>{{ $cuenta_pagar->monto_total }}</td>
                                            <td>{{ $cuenta_pagar->saldo }}</td>
                                            <td>{{ $cuenta_pagar->descripcion }}</td>
                                            <td>{{ date('d/m/Y', strtotime($cuenta_pagar->fecha_registro)) }}</td>
                                            <td class="btns-opciones">
                                                {{-- @if ($cuenta_pagar->saldo > 0)
                                                    <a href="{{ route('cuenta_pagars.edit', $cuenta_pagar->id) }}"
                                                        class="modificar"><i class="fa fa-edit" data-toggle="tooltip"
                                                            data-placement="left" title="Modificar"></i></a>
                                                @endif --}}
                                                <a href="{{ route('cuenta_pagars.show', $cuenta_pagar->id) }}"
                                                    class="evaluar"><i class="fa fa-eye" data-toggle="tooltip"
                                                        data-placement="left" title="Detalle"></i></a>
                                                <a href="#"
                                                    data-url="{{ route('cuenta_pagars.destroy', $cuenta_pagar->id) }}"
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
            columns: [
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
            let cuenta_pagar = $(this).parents('tr').children('td').eq(4).text();
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar el registro <b>${cuenta_pagar}</b>?`);
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
