@extends('layouts.app')

@section('css')
    <style>
        .boton_reporte {
            width: 100% !important;
            margin-left: auto;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .boton_reporte a {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Reportes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Reportes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content" id="contenedorReportes">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h3>Reportes</h3>
                        @if (Auth::user()->tipo == 'ADMINISTRADOR')
                            @include('includes.reporte.reporte_admin')
                        @endif
                        @if (Auth::user()->tipo == 'SUPERVISOR')
                            @include('includes.reporte.reporte_auxiliar')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('reportes.modal.m_usuarios')
    @include('reportes.modal.m_inventario')
    @include('reportes.modal.m_kardex')
    @include('reportes.modal.m_ventas')
    @include('reportes.modal.m_cuentas')
    @include('reportes.modal.m_ventas_diarias_producto')
    @include('reportes.modal.m_ventas_semanales_producto')
    @include('reportes.modal.m_ventas_mensuales_producto')
    @include('reportes.modal.m_ventas_diarias_cajas')
    @include('reportes.modal.m_egreso_caja')
    @include('reportes.modal.m_egresos_caja')
    @include('reportes.modal.m_consumo_diario_clientes')
    @include('reportes.modal.m_consumo_semanal_clientes')
    @include('reportes.modal.m_consumo_mensual_clientes')
    @include('reportes.modal.m_ventas_diarias_credito')
    @include('reportes.modal.m_ventas_semanales_credito')
    @include('reportes.modal.m_ventas_mensuales_credito')
    @include('reportes.modal.m_cuentas_cobrar_fecha')
    @include('reportes.modal.m_cuentas_cobrar_rango_fecha')
    @include('reportes.modal.m_estado_cuenta_cliente')
    @include('reportes.modal.m_detalle_inventario_producto')
    @include('reportes.modal.m_cuenta_pagar')
    @include('reportes.modal.m_saldo_producto')
    @include('reportes.modal.m_resultado_ventas')
    @include('reportes.modal.m_mermas')
    @include('reportes.modal.m_descuento_ventas')
@endsection

@section('scripts')
    <script src="{{ asset('js/reportes/filtro.js') }}"></script>
@endsection
