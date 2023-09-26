@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Caja Central</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('caja_centrals.index') }}">Caja Central</a></li>
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
                            <h3 class="card-title">Modificar Registro</h3>
                        </div>
                        <!-- /.card-header -->
                        {{ Form::model($caja_central, ['route' => ['caja_centrals.update', $caja_central->id], 'method' => 'put']) }}
                        <div class="card-body">
                            @include('caja_centrals.form.form')
                            <button class="btn btn-info"><i class="fa fa-update"></i> ACTUALIZAR</button>
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
        let concepto_id = $("#concepto_id");
        let ingreso_producto_id = $("#ingreso_producto_id");
        let check_lote = $("#check_lote");
        $(document).ready(function() {
            verificaCheckLote();
            check_lote.change(verificaCheckLote)
        });

        function verificaCheckLote() {

            if (check_lote.prop("checked")) {
                ingreso_producto_id.removeClass("oculto");
                ingreso_producto_id.prop("required", true);
                ingreso_producto_id.val("{{ $caja_central->ingreso_producto_id }}");
            } else {
                ingreso_producto_id.addClass("oculto");
                ingreso_producto_id.removeAttr("required");
            }
        }
    </script>
@endsection
