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
                        <li class="breadcrumb-item"><a href="{{ route('ingreso_cajas.index', $caja->id) }}">Ingresos y
                                Egresos >
                                {{ $caja->nombre }}</a></li>
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
                        {{ Form::open(['route' => 'ingreso_cajas.store', 'method' => 'post']) }}
                        <div class="card-body">
                            @include('ingreso_cajas.form.form')

                            <button type="submit" class="btn btn-info" id="btnRegistrar"><i class="fa fa-save"></i>
                                GUARDAR</button>
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
        let tipo_movimiento = $("#tipo_movimiento");
        let sw_egreso = $("#sw_egreso");

        $(document).ready(function() {
            // detectaEgreso();
            // tipo_movimiento.change(detectaEgreso);
            $("#btnRegistrar").parents("form").on("submit", function() {
                $("#btnRegistrar").prop("disabled", true);
            });
        });

        function detectaEgreso() {
            if (tipo_movimiento.val() == 'EGRESO') {
                sw_egreso.parent().removeClass("oculto");
                sw_egreso.prop("required", true);
            } else {
                sw_egreso.val("");
                sw_egreso.parent().addClass("oculto");
                sw_egreso.removeAttr("required");
            }
        }
    </script>
@endsection
