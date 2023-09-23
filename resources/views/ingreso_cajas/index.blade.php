@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ingresos y Egresos > <div class="badge bg-red text-lg">{{ $caja->nombre }}</div>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Ingresos y Egresos > {{ $caja->nombre }}</li>
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
                                        {{ $saldo_actual }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-primary text-lg font-weight-bold mt-2 text-center">BANCO:
                                        {{ $suma_bancos }}</div>
                                </div>
                                <div class="col-md-3 mr-auto">
                                    <div class="alert bg-orange text-lg font-weight-bold mt-2 text-center">CAJA:
                                        {{ $suma_otros }}</div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-4 mt-0">
                                <div class="col-md-12">
                                    @if (Auth::user()->tipo != 'CAJA')
                                        <a href="{{ route('cajas.index', $caja->id) }}" class="btn btn-default"><i
                                                class="fa fa-arrow-left"></i>
                                            Volver</a>
                                    @endif
                                    <a href="{{ route('ingreso_cajas.create', $caja->id) }}" class="btn btn-info"><i
                                            class="fa fa-plus"></i>
                                        Ingresos y Egresos</a>
                                    @if (Auth::user()->tipo == 'CAJA')
                                        <a href="{{ route('ventas.create', $caja->id) }}" class="btn btn-danger"><i
                                                class="fa fa-plus"></i>
                                            Nueva venta</a>
                                        <a href="{{ route('cuenta_cobrars.create') }}" class="btn btn-success"><i
                                                class="fa fa-plus"></i>
                                            Cuentas por cobrar</a>
                                    @endif
                                </div>
                            </div>
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Concepto</th>
                                        <th>Monto</th>
                                        <th>Descripción</th>
                                        <th>Tipo de Movimiento</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($ingreso_cajas as $ingreso_caja)
                                        <tr>
                                            <td>{{ date('d/m/Y', strtotime($ingreso_caja->fecha)) }}</td>
                                            <td>{{ date('H:i:s', strtotime($ingreso_caja->hora)) }}</td>
                                            <td>{{ $ingreso_caja->concepto ? $ingreso_caja->concepto->nombre : '-' }}</td>
                                            <td>{{ $ingreso_caja->monto_total }}</td>
                                            <td>{{ $ingreso_caja->descripcion_txt }}</td>
                                            <td>{{ $ingreso_caja->tipo_movimiento }}</td>
                                            <td class="btns-opciones">
                                                @if (Auth::user()->tipo != 'CAJA')
                                                    {{-- <a href="{{ route('ingreso_cajas.edit', $ingreso_caja->id) }}"
                                                        class="modificar"><i class="fa fa-edit" data-toggle="tooltip"
                                                            data-placement="left" title="Modificar"></i></a> --}}

                                                    <a href="{{ route('ingreso_cajas.show', $ingreso_caja->id) }}"
                                                        class="evaluar"><i class="fa fa-eye" data-toggle="tooltip"
                                                            data-placement="left" title="Ver detalle"></i></a>

                                                    @if ($ingreso_caja->estado == 1)
                                                        <a href="#"
                                                            data-url="{{ route('ingreso_cajas.destroy', $ingreso_caja->id) }}"
                                                            data-toggle="modal" data-target="#modal-eliminar"
                                                            class="eliminar"><i class="fa fa-trash" data-toggle="tooltip"
                                                                data-placement="left" title="Eliminar"></i></a>
                                                    @endif
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
            let fecha = $(this).parents('tr').children('td').eq(0).text();
            let hora = $(this).parents('tr').children('td').eq(1).text();
            let descripcion = $(this).parents('tr').children('td').eq(4).text();
            let movimiento = $(this).parents('tr').children('td').eq(5).text();
            $('#mensajeEliminar').html(
                `<p class="mb-1 text-lg">¿Está seguro(a) de eliminar este registro?</p>
                <div class="card">
                    <div class="card-body bg-danger">
                        <p class="mb-1">Fecha y hora: <b>${fecha} ${hora}</b></p>
                        <p class="mb-1">Descripción: <b>${descripcion}</b></p>
                        <p class="mb-1">Movimiento: <b>${movimiento}</b></p>    
                    </div>    
                </div>
                <h4 class="text-md font-weight-bold">- Esto eliminará/deshará aquellos registros que esten relacionados a este</h4>
                <h4 class="text-md font-weight-bold">- Esta acción no se podrá deshacer despúes</h4>`
            );
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
