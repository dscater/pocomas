@extends('layouts.app')

@section('css')
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
                        <li class="breadcrumb-item"><a href="{{ route('lote_productos.index') }}">Ingresos Productos</a>
                        </li>
                        <li class="breadcrumb-item active">Modificar</li>
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
                            <h3 class="card-title">Modificar Ingreso</h3>
                        </div>
                        <!-- /.card-header -->
                        {{ Form::model($ingreso_producto, ['route' => ['lote_productos.update', $ingreso_producto->id], 'method' => 'put']) }}
                        <div class="card-body">
                            @include('lote_productos.form.form')
                            <button class="btn btn-info" id="btnRegistrar"><i class="fa fa-update"></i> ACTUALIZAR</button>
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
@endsection

@section('scripts')
    <script>
        let precio_total = $("#precio_total");
        let precio_compra = $("#precio_compra");
        let kilos = $("#kilos");
        $(document).ready(function() {
            precio_compra.on('change keyup', getPrecioTotal);
            kilos.on('change keyup', getPrecioTotal);
        });

        function getPrecioTotal() {
            if (precio_compra.val() != '' && kilos.val() != '') {
                let total = parseFloat(precio_compra.val()) * parseFloat(kilos.val());
                total = parseFloat(total).toFixed(2);
                precio_total.val(total);
            } else {
                precio_total.val('0.00');
            }
        }
    </script>
@endsection
