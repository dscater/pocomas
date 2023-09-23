@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Conceptos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Conceptos</li>
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
                            <a href="{{ route('conceptos.create') }}" class="btn btn-info"><i class="fa fa-plus"></i>
                                Nuevo</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table data-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>Nombre</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                    @endphp
                                    @foreach ($conceptos as $concepto)
                                        <tr>
                                            <td>{{ $cont++ }}</td>
                                            <td>{{ $concepto->nombre }}</td>
                                            <td class="btns-opciones">
                                                <a href="{{ route('conceptos.edit', $concepto->id) }}" class="modificar"><i
                                                        class="fa fa-edit" data-toggle="tooltip" data-placement="left"
                                                        title="Modificar"></i></a>

                                                <a href="#" data-url="{{ route('conceptos.destroy', $concepto->id) }}"
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
            let concepto = $(this).parents('tr').children('td').eq(2).text();
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar el registro <b>${concepto}</b>?`);
            let url = $(this).attr('data-url');
            console.log($(this).attr('data-url'));
            $('#formEliminar').prop('action', url);
        });

        $('#btnEliminar').click(function() {
            $('#formEliminar').submit();
        });

        // CERRAR CAJA
        $(document).on('click', 'table tbody tr td.btns-opciones a.cerrar_concepto', function(e) {
            e.preventDefault();
            let concepto = $(this).parents('tr').children('td').eq(2).text();
            let concepto_id = $(this).attr('data-id');
            let saldo = $(this).attr('data-saldo');
            $('#mensajeCerrarConcepto').html(
                `¿Está seguro(a) de cerrar la concepto <b>${concepto}</b>?<br>Saldo actual: ${saldo}<input type="hidden" name="concepto_id" value="${concepto_id}"/><input type="hidden" name="monto_total" value="${saldo}"/>`
            );
            let url = $(this).attr('data-url');
            $('#formCerrarConcepto').prop('action', url);
        });

        $('#btnCerrarConcepto').click(function() {
            $('#formCerrarConcepto').submit();
        });
    </script>
@endsection
@endsection
