@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Mermas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('mermas.index') }}">Mermas</a></li>
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
                            <h3 class="card-title">Nuevo Registro</h3>
                        </div>
                        <!-- /.card-header -->
                        {{ Form::open(['route' => 'mermas.store', 'method' => 'post']) }}
                        <div class="card-body">
                            @include('mermas.form.form')

                            <button class="btn btn-info"><i class="fa fa-save"></i> GUARDAR</button>
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
    <input type="hidden" value="{{ route('ingreso_productos.getProductosLote') }}" id="url_productos_lote">
@endsection
@section('scripts')
    <script>
        let ingreso_producto_id = $("#ingreso_producto_id");
        let detalle_ingreso_id = $("#detalle_ingreso_id");

        ingreso_producto_id.on("change keyup", getProductosLote);

        function getProductosLote() {
            if (ingreso_producto_id.val() != '') {
                $.ajax({
                    type: "GET",
                    url: $("#url_productos_lote").val(),
                    data: {
                        id: ingreso_producto_id.val(),
                    },
                    dataType: "json",
                    success: function(response) {
                        detalle_ingreso_id.html(response);
                    }
                });
            } else {
                detalle_ingreso_id.html("");
            }
        }
    </script>
@endsection
