@extends('layouts.app')

@section('css')
    <style>
        .contenedor_productos {
            height: auto;
            max-height: 600px;
            overflow: auto;
            transition: max-height 0.5s;
        }

        .contenedor_productos.oculto {
            max-height: 0;
        }
    </style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ingresos Productos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ingreso_productos.index') }}">Ingresos Productos</a>
                        </li>
                        <li class="breadcrumb-item active">Nuevo</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- /.card-header -->
                    {{ Form::open(['route' => 'ingreso_productos.store', 'method' => 'post']) }}
                    @include('ingreso_productos.form.form')
                    <div class="row mt-1">
                        <div class="col-md-4 ml-auto mr-auto">
                            <button class="btn btn-info btn-block" id="btnRegistrar"><i class="fa fa-save"></i>
                                GUARDAR</button>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4 ml-auto mr-auto">
                            <button type="button" class="btn btn-default btn-block" id="btnCancelar"><i
                                    class="fa fa-times"></i>
                                CANCELAR</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                    <!-- /.card-body -->
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h4 class="w-100 text-center text-md font-weight-bold">LISTA DE INGRESOS</h4>
                </div>
            </div>
            <div id="contenedor_listado"></div>
        </div>
    </section>

    <input type="hidden" id="urlInfoIngresoProducto"value="{{ route('lote_productos.getInfoParaRegistroIngreso') }}">
    <input type="hidden" id="urlListaIngresoProductos"value="{{ route('ingreso_productos.index') }}">
@endsection
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
    </script>
    <script src="{{ asset('js/ingreso_productos/create.js') }}"></script>
    <script src="{{ asset('js/ingreso_productos/index.js') }}"></script>
@endsection
