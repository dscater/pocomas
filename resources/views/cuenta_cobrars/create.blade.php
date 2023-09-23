@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/cuenta_cobrars/create.css') }}">
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
                        <li class="breadcrumb-item"><a href="{{ route('cuenta_cobrars.index') }}">Cuentas por Cobrar</a>
                        </li>
                        <li class="breadcrumb-item active">Registrar Pagos</li>
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
                            <h3 class="card-title">Registro de Pago</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                            <div class="row">
                                @if (Auth::user()->tipo != 'CAJA')
                                    {{-- <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Seleccionar Caja*</label>
                                            {{ Form::select('caja_id', $array_cajas, null, ['class' => 'form-control', 'id' => 'caja_id']) }}
                                        </div>
                                    </div> --}}
                                @else
                                    <input type="hidden" id="caja_id">
                                @endif
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Seleccionar Cliente*</label>
                                        <small>(Se mostrarán todas las cuentas por cobrar del cliente seleccionado)</small>
                                        {{ Form::select('cliente', $array_clientes, null, ['class' => 'form-control select2', 'id' => 'select_cliente']) }}
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div id="contenedor_cuentas">

                                    </div>
                                </div>
                                <div class="col-md-12 oculto" id="formulario_pago">
                                    <form action="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Total cancelar:</label>
                                                    <input type="number" id="i_total_cancelar" class="form-control"
                                                        min="1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Saldo:</label>
                                                    <input type="number" id="i_saldo" class="form-control" min="0"
                                                        required readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Descripción:</label>
                                                    <textarea class="form-control" rows="2" name="observacion" id="observacion"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Tipo de cobro:</label>
                                                    <select name="tipo_cobro" id="tipo_cobro" class="form-control" required>
                                                        <option value="BANCO">BANCO</option>
                                                        <option value="CAJA">CAJA</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-danger btn-block"
                                                        id="btn_registrar_pago">Registrar pago</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <a href="{{ url()->previous() }}" class="btn btn-default">Volver</a>
                            <a href="{{ route('cuenta_cobrars.index') }}" class="btn btn-primary">Cuentas por Cobrar</a>
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

    <input type="hidden" id="urlCuentasClientes" value="{{ route('clientes.cuentas_cobrar') }}">
    <input type="hidden" id="urlDetalleOrden" value="{{ route('cuenta_cobrars.getDetalleOrden') }}">
    <input type="hidden" id="urlRegistraPago" value="{{ route('cuenta_cobrars.registrarPago') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/cuenta_cobrars/create.js') }}"></script>
@endsection
