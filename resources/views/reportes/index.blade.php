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
    @include('reportes.modal.m_resultado_operacion')
    @include('reportes.modal.m_indicadores')
    @include('reportes.modal.m_entrega_mb')
    
@endsection

@section('scripts')
    <script src="{{ asset('js/reportes/filtro.js') }}"></script>
@endsection
