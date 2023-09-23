@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cuentas por pagar</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cuenta_pagars.index') }}">Cuentas por pagar</a></li>
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
                        {{ Form::open(['route' => 'cuenta_pagars.store', 'method' => 'post', 'id' => 'formCuentaPagar']) }}
                        <div class="card-body">
                            @include('cuenta_pagars.form.form')

                            <button class="btn btn-info" type="button"id="btn_registrar_pago"><i class="fa fa-save"></i>
                                GUARDAR</button>
                            <a href="{{ url()->previous() }}" class="btn btn-default">VOLVER</a>
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

    <input type="hidden" id="urlGetCuentas" value="{{ route('ingreso_productos.getIngreso') }}">
@endsection
@section('scripts')
    <script src="{{ asset('js/cuenta_pagars/create.js') }}"></script>
@endsection
