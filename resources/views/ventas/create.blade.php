@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/ventas/create.css') }}">
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
                        <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Nueva Venta</h3>
                        </div>
                        <!-- /.card-header -->
                        {{ Form::open(['route' => 'ventas.store', 'method' => 'post', 'id' => 'formulario']) }}
                        <div class="card-body">
                            @include('ventas.form.form')
                            <div class="row">
                                <div class="col-md-12 p-0 mt-2">
                                    <button class="btn btn-danger btn-block" id="btnRegistrarVenta"><i
                                            class="fa fa-save"></i>
                                        REGISTRAR
                                        VENTA</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
    </section>
    <input type="hidden" id="urlInfoCliente" value="{{ route('clientes.getInfoCliente') }}">
    <input type="hidden" id="urlInfoVenta" value="{{ route('productos.getInfoVenta') }}">
    <input type="hidden" id="urlProductosLote" value="{{ route('ingreso_productos.getProductosLote') }}">

    @include('modal.nuevo_cliente')
@endsection
@section('scripts')
    <script src="{{ asset('js/ventas/create.js') }}"></script>
@endsection
