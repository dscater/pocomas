@extends('layouts.app')

@section('css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ingresos y Egresos > <div class="badge bg-red text-lg">
                            {{ $ingreso_caja->caja->nombre }}</div>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('ingreso_cajas.index', $ingreso_caja->caja->id) }}">Ingresos y Egresos >
                                {{ $ingreso_caja->caja->nombre }}</a></li>
                        <li class="breadcrumb-item active">Ver Detalle</li>
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
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>{{ $ingreso_caja->tipo }}</h4>
                                        </div>
                                        <div
                                            class="card-body {{ $ingreso_caja->tipo_movimiento == 'INGRESO' ? 'bg-success' : 'bg-danger' }}">
                                            <p class="text-xl">
                                                <strong>Total registrado:
                                                </strong>
                                                <span class="font-weight-bold">{{ $ingreso_caja->monto_total }}</span>
                                            </p>
                                            <p><strong>Fecha y hora:</strong>
                                                {{ date('d/m/Y', strtotime($ingreso_caja->fecha)) }}
                                                {{ date('H:i:s', strtotime($ingreso_caja->hora)) }}</p>
                                            <p><strong>Movimiento:</strong>
                                                {{ $ingreso_caja->tipo_movimiento }}</p>
                                            @if ($ingreso_caja->concepto)
                                                <p><strong>Concepto:</strong>
                                                    {{ $ingreso_caja->concepto->nombre }}</p>
                                            @endif
                                            <p class="mb-0"><strong>Descripción:</strong>
                                                {{ $ingreso_caja->descripcion_txt }}</p>
                                        </div>
                                    </div>
                                </div>
                                @if (
                                    $ingreso_caja->tipo == 'VENTA' ||
                                        $ingreso_caja->tipo == 'CANCELACIÓN DE ANTICIPO' ||
                                        $ingreso_caja->tipo == 'ANTICIPO VENTA')
                                    @if ($ingreso_caja->venta)
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4>Detalle Venta</h4>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Fecha:</strong> {{ $ingreso_caja->venta->fecha_venta }}</p>
                                                    <p><strong>Monto total:</strong>
                                                        {{ $ingreso_caja->venta->monto_total }}
                                                    </p>
                                                    @if ($ingreso_caja->venta->tipo_venta == 'ANTICIPOS' && $ingreso_caja->tipo == 'CANCELACIÓN DE ANTICIPO')
                                                        <p><strong>Saldo:</strong> {{ $ingreso_caja->venta->saldo }}
                                                        </p>
                                                    @else
                                                        <p><strong>Saldo:</strong>
                                                            {{ number_format($ingreso_caja->venta->monto_total - $ingreso_caja->monto_total, 2, '.', '') }}
                                                        </p>
                                                    @endif

                                                    <p><strong>Tipo de venta:</strong>
                                                        {{ $ingreso_caja->venta->tipo_venta }}</p>
                                                    @if ($ingreso_caja->venta->tipo_venta == 'ANTICIPOS' && $ingreso_caja->tipo == 'CANCELACIÓN DE ANTICIPO')
                                                        <p><strong>Monto anticipo:</strong>
                                                            {{ number_format($ingreso_caja->venta->monto_total - $ingreso_caja->monto_total, 2, '.', '') }}
                                                        </p>
                                                    @else
                                                        <p><strong>Monto anticipo:</strong>
                                                            {{ $ingreso_caja->monto_total }}
                                                        </p>
                                                    @endif
                                                    <p><strong>Monto cobrado:</strong>
                                                        {{ $ingreso_caja->venta->monto_recibido }}</p>
                                                        
                                                    <p><strong>Monto cambio:</strong>
                                                        {{ $ingreso_caja->venta->monto_cambio }}</p>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <th width="20px">Nº</th>
                                                            <th>Producto</th>
                                                            <th>C/U</th>
                                                            <th width="120px">Cantidad</th>
                                                            <th width="120px">Total S/D</th>
                                                            <th width="120px">Descuento</th>
                                                            <th width="120px">Total</th>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $cont = 1;
                                                            @endphp
                                                            @foreach ($ingreso_caja->venta->detalle as $vd)
                                                                <tr>
                                                                    <td>{{ $cont++ }}</td>
                                                                    <td>{{ $vd->producto->nombre }}</td>
                                                                    <td>{{ $vd->producto->precio }}</td>
                                                                    <td>{{ $vd->cantidad }}</td>
                                                                    <td>{{ $vd->cantidad * $vd->producto->precio }}</td>
                                                                    <td>{{ $vd->descuento }}</td>
                                                                    <td>{{ $vd->sub_total }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @elseif($ingreso_caja->tipo == 'PAGO POR COBRAR')
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Estado Actual - Cuenta cliente</h4>
                                            </div>
                                            <div class="card-body">
                                                <p><strong>Cliente:</strong>
                                                    {{ $ingreso_caja->cuenta_pago->cuenta_cliente->cliente->nombre }}</p>
                                                <p><strong>Monto acumulado:</strong>
                                                    {{ $ingreso_caja->cuenta_pago->cuenta_cliente->total_deuda }}</p>
                                                <p><strong>Saldo restante:</strong>
                                                    {{ $ingreso_caja->cuenta_pago->cuenta_cliente->saldo }}</p>
                                                <p>
                                                    <strong>Estado: </strong>
                                                    @if ($ingreso_caja->cuenta_pago->cuenta_cliente->estado == 'PENDIENTE')
                                                        <span
                                                            class="badge badge-danger text-xs">{{ $ingreso_caja->cuenta_pago->cuenta_cliente->estado }}</span>
                                                    @else
                                                        <span
                                                            class="badge badge-success text-xs">{{ $ingreso_caja->cuenta_pago->cuenta_cliente->estado }}</span>
                                                    @endif
                                                </p>
                                                @php
                                                    $cuentas = App\CuentaCobrar::where('cliente_id', $ingreso_caja->cuenta_pago->cuenta_cliente->cliente_id)
                                                        ->where('status', 1)
                                                        ->orderBy('created_at', 'asc')
                                                        ->get();
                                                @endphp
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="bg-danger">
                                                            <th width="100px">Nro. Orden</th>
                                                            <th>Monto total</th>
                                                            <th>Saldo</th>
                                                            <th>Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $total_monto = 0;
                                                            $total_saldo = 0;
                                                        @endphp
                                                        @foreach ($cuentas as $value)
                                                            @php
                                                                $nro_factura = (int) $value->venta->factura->nro_factura;
                                                                if ($nro_factura < 10) {
                                                                    $nro_factura = '000' . $nro_factura;
                                                                } elseif ($nro_factura < 100) {
                                                                    $nro_factura = '00' . $nro_factura;
                                                                } elseif ($nro_factura < 1000) {
                                                                    $nro_factura = '0' . $nro_factura;
                                                                }
                                                            @endphp
                                                            <tr class="fila" data-id="{{ $value->id }}">
                                                                <td>{{ $nro_factura }}</td>
                                                                <td data-val="{{ $value->monto_deuda }}" data-pago="0">
                                                                    {{ $value->monto_deuda }}</td>
                                                                <td data-val="{{ $value->saldo }}" data-pago="0">
                                                                    {{ $value->saldo }}</td>
                                                                <td data-val="{{ $value->saldo }}" data-pago="0">
                                                                    @if ($value->estado == 'PENDIENTE')
                                                                        <span
                                                                            class="badge badge-danger text-xs">{{ $value->estado }}</span>
                                                                    @else
                                                                        <span
                                                                            class="badge badge-success text-xs">{{ $value->estado }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $total_monto += (float) $value->monto_deuda;
                                                                $total_saldo += (float) $value->saldo;
                                                            @endphp
                                                        @endforeach
                                                        <tr class="bg-primary font-weight-bold text-lg">
                                                            <td>TOTAL</td>
                                                            <td id="total_monto">
                                                                {{ number_format($total_monto, 2, '.', '') }}</td>
                                                            <td id="total_saldo">
                                                                {{ number_format($total_saldo, 2, '.', '') }}</td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('ingreso_cajas.index', $ingreso_caja->caja->id) }}"
                                        class="btn btn-default btn-block"><i class="fa fa-arrow-left"></i> Volver</a>
                                </div>
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
@endsection
@section('scripts')
    <script></script>
@endsection
