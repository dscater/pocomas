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
                        <li class="breadcrumb-item active">Detalle</li>
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
                            <h3 class="card-title">Detalle Registro</h3>
                        </div>
                        <!-- /.card-header -->
                        @php
                            $ingreso_producto = $cuenta_pagar->ingreso_producto;
                        @endphp
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nro. Lote*</label>
                                        {{ Form::text('nro_lote', $ingreso_producto->nro_lote, ['class' => 'form-control', 'readonly']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Detalle</h5>
                                    <div class="card">
                                        <div class="card-body" id="contenedorDetalle">
                                            @include('parcial.detalle_ingreso_producto')
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Lista de Pagos</h5>
                                    <div class="card">
                                        <div class="card-body" id="contenedorListaPagos">
                                            @php
                                                $cuenta_pagar_detalles = App\CuentaPagarDetalle::where('cuenta_pagar_id', $ingreso_producto->cuenta_pagars->id)
                                                    ->where('monto', '>', 0)
                                                    ->orderBy('created_at', 'asc')
                                                    ->get();
                                            @endphp
                                            @include('parcial.lista_pagos_ingreso_productos')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ url()->previous() }}" class="btn btn-default">VOLVER</a>
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
@endsection

@section('scripts')
@endsection
