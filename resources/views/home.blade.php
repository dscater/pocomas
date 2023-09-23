@extends('layouts.app')

@section('background-image')
    {{-- style="background-image:url({{ asset('imgs/fondo.jpg') }})" --}}
@endsection

@section('content')
    @php
    $nombre_usuario = '';
    if (Auth::user()->datosUsuario) {
        $nombre_usuario =
            Auth::user()->datosUsuario->nombre .
            ' ' .
            Auth::user()->datosUsuario->paterno .
            '
                    ' .
            Auth::user()->datosUsuario->materno;
    } else {
        $nombre_usuario = Auth::user()->name;
    }
    @endphp
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-black">Panel</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if (Auth::user()->tipo == 'ADMINISTRADOR')
                @include('includes.home.home_admin')
            @endif
            @if (Auth::user()->tipo == 'AUXILIAR')
                @include('includes.home.home_auxiliar')
            @endif
            @if (Auth::user()->tipo == 'CAJA')
                @include('includes.home.home_caja')
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold">COAX - SISTEMA WEB DE EXPOSICIÓN DE PRODUCTOS Y PUNTOS DE VENTAS</h3>
                            <h4>Bienvenido(a) {{ $nombre_usuario }}</h4>
                        </div>
                    </div>
                </div>
                @if (Auth::user()->tipo != 'CAJA' && count($minimos) > 0)
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>PRODUCTOS CON STOCK IGUAL O MENOR AL MÍNIMO PERMITIDO</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <th width="10px">Nº</th>
                                        <th>Producto</th>
                                        <th>Stock Actual</th>
                                        <th>Stock Mínimo</th>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cont = 1;
                                        @endphp
                                        @foreach ($minimos as $m)
                                            <tr>
                                                <td>{{ $cont++ }}</td>
                                                <td>{{ $m->nombre }}</td>
                                                <td>{{ $m->stock_actual }}</td>
                                                <td>{{ $m->stock_minimo }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!--/. container-fluid -->
    </section>
    <!-- /.content -->

@endsection

@section('scripts')
    <script>
        @if (session('bien'))
            mensajeNotificacion('{{ session('bien') }}','success');
        @endif

        @if (session('info'))
            mensajeNotificacion('{{ session('info') }}','info');
        @endif

        @if (session('error'))
            mensajeNotificacion('{{ session('error') }}','error');
        @endif

        @if (Auth::user()->tipo != 'CAJA' && count($minimos) > 0)
        mensajeNotificacion('Existen productos con el stock menor o igual al permitido','info');
        @endif

        $('table.data-table').DataTable({
            responsive: true,
            order: [
                [0, 'desc']
            ],
            columns: [
                null,
                {
                    width: "5%"
                },
                null,
                null,
                {
                    width: "15%"
                },
                null,
                {
                    width: "5%"
                },
                {
                    width: "5%"
                },
                null,
                {
                    width: "5%"
                },
            ],
            scrollCollapse: true,
            language: lenguaje,
            pageLength: 25
        });


        // ELIMINAR
        $(document).on('click', 'table tbody tr td.btns-opciones a.eliminar', function(e) {
            e.preventDefault();
            let itinerario = $(this).parents('tr').children('td').eq(1).text();
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar la itinerario <b>${itinerario}</b>?`);
            let url = $(this).attr('data-url');
            console.log($(this).attr('data-url'));
            $('#formEliminar').prop('action', url);
        });

        $('#btnEliminar').click(function() {
            $('#formEliminar').submit();
        });

    </script>
@endsection
