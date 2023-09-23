@extends('layouts.app')

@section('css')
<style>
    .bordeado{
        border-width: 2px!important;
    }
</style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cuentas por Cobrar</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cuenta_cobrars.index') }}">Cuentas por Cobrar</a></li>
                        <li class="breadcrumb-item active">Pagos</li>
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
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('cuenta_cobrars.index') }}" class="btn btn-default btn-block"><i
                                            class="fa fa-arrow-left"></i>
                                        Cuentas por cobrar</a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('cuenta_cobrars.create') }}" class="btn btn-info btn-block"><i
                                            class="fa fa-plus"></i>
                                        Registrar Pagos</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="card col-md-12">
                                    <div class="card-header">
                                        <h4 class="font-weight-bold">Información de cuenta</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p>
                                                    <strong>Cliente: </strong>{{ $cuenta_cliente->cliente->nombre }}
                                                </p>
                                                <p>
                                                    <strong>Total acumulado:
                                                    </strong>{{ number_format($cuenta_cliente->total_deuda, 2, '.', '') }}
                                                </p>
                                                <p>
                                                    <strong>Saldo restante:
                                                    </strong>{{ number_format($cuenta_cliente->saldo, 2, '.', '') }}
                                                </p>
                                                <p class="mb-0">
                                                    <strong>Estado: </strong>
                                                    @if ($cuenta_cliente->estado == 'PENDIENTE')
                                                        <span
                                                            class="badge badge-danger text-xs">{{ $cuenta_cliente->estado }}</span>
                                                    @else
                                                        <span
                                                            class="badge badge-success text-xs">{{ $cuenta_cliente->estado }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="font-weight-bold w-100 text-center">PAGOS REALIZADOS:</h4>
                                @php
                                    $cuenta_pagos = App\CuentaPago::where('cuenta_id', $cuenta_cliente->id)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                @endphp
                                @php
                                    $nro_pago = count($cuenta_pagos);
                                @endphp
                                @foreach ($cuenta_pagos as $key => $cp)
                                    <div class="card col-md-12 {{ $key == 0 ? 'border border-success bordeado' : '' }}">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p>
                                                        <strong>Nro. Pago: </strong>{{ $nro_pago-- }}
                                                    </p>
                                                    <p>
                                                        <strong>Fecha: </strong>{{ $cp->fecha_pago }}
                                                    </p>
                                                    <p>
                                                        <strong>Tipo de cobro: </strong>{{ $cp->tipo_cobro }}
                                                    </p>
                                                    <p class="mb-0">
                                                        <strong>Total cancelado:
                                                        </strong>{{ number_format($cp->monto, 2, '.', '') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
@endsection
@endsection
