@if (count($ingreso_productos) > 0)
    <div class="row">
        @foreach ($ingreso_productos as $ingreso_producto)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger">
                        <h4 class="card-title font-weight-bold">
                            Nro. Lote: {{ $ingreso_producto->nro_lote }}
                        </h4>
                    </div>
                    @if (count($ingreso_producto->detalle_ingresos) > 0)
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock kilos</th>
                                    <th>Stock cantidad</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ingreso_producto->detalle_ingresos as $di)
                                    <tr>
                                        <td>{{ $di->producto->nombre }}</td>
                                        <td>{{ $di->stock_kilos }}</td>
                                        <td>{{ $di->stock_cantidad }}</td>
                                        <td>
                                            <span
                                                class="badge text-sm {{ $di->producto_id == $ingreso_producto->producto_id ? 'badge-success' : 'badge-warning' }}">{{ $di->producto_id == $ingreso_producto->producto_id ? 'PRINCIPAL' : 'DERRIVADO' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-center"><strong>Kilos disponibles: </strong><span
                                    class="badge badge-primary text-md">{{ $ingreso_producto->kilos_venta }}</span>
                            </div>
                            <div class="col-md-6 text-center"><strong>Cantidad disponible:
                                </strong><span
                                    class="badge badge-info text-md">{{ $ingreso_producto->cantidad_venta }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        {{ $ingreso_productos->links() }}
    </div>
@else
    <div class="row">
        <div class="col-md-12">
            <h4 class="w-100 text-center font-weight-bold text-sm text-gray">AÚN NO HAY REGISTROS</h4>
        </div>
    </div>
@endif
