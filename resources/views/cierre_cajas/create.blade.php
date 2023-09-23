@extends('layouts.app')

@section('css')
    <style>
        #contendor_monto {
            border: solid 1px;
            padding: 10px;
            background: black;
            text-align: center;
            color: white;
            font-size: 1.4em;
        }

    </style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cierre de Cajas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cierre_cajas.index') }}">Cierre de Cajas</a></li>
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
                            <h3 class="card-title">Nuevo Cierre</h3>
                        </div>
                        <!-- /.card-header -->
                        {{ Form::open(['route' => 'cierre_cajas.store', 'method' => 'post']) }}
                        <div class="card-body">
                            @include('cierre_cajas.form.form')
                            <button class="btn btn-info" id="btnRegistraCierre"><i class="fa fa-save"></i> GUARDAR</button>
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
    <input type="hidden" value="{{ route('cierre_cajas.getUltimoMontoCaja') }}" id="urlUltimoMontoCaja">
@endsection
@section('scripts')
@if(Auth::user()->tipo == 'ADMINISTRADOR')
<script src="{{asset('js/cierre_cajas/create.js')}}"></script>
@endif
@endsection
