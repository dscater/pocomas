@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ventas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Ventas</li>
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
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('ventas.anticipos') }}" class="btn btn-warning btn-sm btn-block"><i
                                            class="fa fa-list"></i>
                                        Anticipos</a>
                                </div>
                                @if (Auth::user()->tipo == 'CAJA')
                                    <div class="col-md-3">
                                        <a href="{{ route('ventas.create') }}" class="btn btn-info btn-block"><i
                                                class="fa fa-plus"></i>
                                            Nueva venta</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha Venta</th>
                                        <th>Caja</th>
                                        <th>Usuario</th>
                                        <th>Cliente</th>
                                        <th>Cantidad Kilos</th>
                                        <th>Cantidad Cerdos</th>
                                        <th>Anticipo</th>
                                        <th>Saldo</th>
                                        <th>Monto Total</th>
                                        <th>Tipo Venta</th>
                                        {{-- <th>Concepto</th> --}}
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($ventas as $venta)
                                        <tr>
                                            <td>{{ $venta->fecha_venta }}</td>
                                            <td>{{ $venta->caja->nombre }}</td>
                                            <td>{{ $venta->user->name }}</td>
                                            <td>{{ $venta->cliente->nombre }}</td>
                                            <td>{{ $venta->cantidad_total_kilos }}</td>
                                            <td>{{ $venta->cantidad_total }}</td>
                                            <td>{{ $venta->anticipo }}</td>
                                            <td>{{ $venta->saldo }}</td>
                                            <td>{{ $venta->monto_total }}</td>
                                            <td>{{ $venta->tipo_venta }}</td>
                                            {{-- <td>{{ $venta->caja->ingresos()->where('tipo', 'VENTA')->where('registro_id', $venta->id)->first()
                                                ? ($venta->caja->ingresos()->where('tipo', 'VENTA')->where('registro_id', $venta->id)->first()->concepto
                                                    ? $venta->caja->ingresos()->where('tipo', 'VENTA')->where('registro_id', $venta->id)->first()->concepto->nombre
                                                    : '-')
                                                : '-' }}
                                            </td> --}}
                                            <td class="btns-opciones">
                                                @if ($venta->tipo_venta == 'ANTICIPOS' && (float) $venta->saldo > 0)
                                                    <a href="#"
                                                        data-url="{{ route('ventas.confirmar_venta', $venta->id) }}"
                                                        data-toggle="modal" data-target="#modal_confirmar_venta"
                                                        class="evaluar confirmar_venta"><i class="fa fa-check"
                                                            data-toggle="tooltip" data-placement="left"
                                                            title="Confirmar Venta"></i></a>
                                                @endif

                                                <a href="{{ route('ventas.show', $venta->id) }}" class="ir-evaluacion"
                                                    data-toggle="tooltip" title="Factura"><i class="fa fa-file-pdf"></i></a>

                                                {{-- @if (Auth::user()->tipo == 'ADMINISTRADOR')
                                                    <a href="{{ route('ventas.edit', $venta->id) }}" class="modificar"><i
                                                            class="fa fa-edit" data-toggle="tooltip" data-placement="left"
                                                            title="Modificar"></i></a>

                                                    <a href="#" data-url="{{ route('ventas.destroy', $venta->id) }}"
                                                        data-toggle="modal" data-target="#modal-eliminar"
                                                        class="eliminar"><i class="fa fa-trash" data-toggle="tooltip"
                                                            data-placement="left" title="Eliminar"></i></a>
                                                @endif --}}
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
    @include('modal.confirmar_venta')

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

        @if (session('url_orden'))
            location = "{{ session('url_orden') }}?imprime=true";
        @endif

        $('table.data-table').DataTable({
            order: [
                [0, 'desc']
            ],
            columns: [{
                    width: "10%"
                },
                null,
                null,
                null,
                {
                    width: "10%"
                },
                {
                    width: "10%"
                },
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
            let venta = $(this).parents('tr').children('td').eq(1).text();
            $('#mensajeEliminar').html(
                `¿Está seguro(a) de eliminar el registro de la caja <b>${venta}</b>?<br><h4>Esto eliminara también todos los pagos registrados de esta venta</h4>`
            );
            let url = $(this).attr('data-url');
            console.log($(this).attr('data-url'));
            $('#formEliminar').prop('action', url);
        });

        $('#btnEliminar').click(function() {
            $('#formEliminar').submit();
        });

        // CONFIRMAR VENTA
        $(document).on('click', 'table tbody tr td.btns-opciones a.confirmar_venta', function(e) {
            e.preventDefault();
            let venta = $(this).parents('tr').children('td').eq(3).text();
            let saldo = $(this).parents('tr').children('td').eq(6).text();
            let caja = $(this).parents('tr').children('td').eq(1).text();
            $('#mensajeConfirmarVenta').html(
                `¿Está seguro(a) de confirmar la venta del cliente <b>${venta}</b> con saldo <b>${saldo}</b>?<br>Se registrará el monto de <b>Bs. ${saldo}</b> en la caja <b>${caja}</b>`
            );
            let url = $(this).attr('data-url');
            $('#formConfirmarVenta').prop('action', url);
        });

        $('#btnConfirmarVenta').click(function() {
            $('#formConfirmarVenta').submit();
        });
    </script>
@endsection
@endsection
