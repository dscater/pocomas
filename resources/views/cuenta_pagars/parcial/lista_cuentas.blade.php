<table class="table table-bordered">
    <thead>
        <tr class="bg-danger">
            <th width="100px">Nro. de Lote</th>
            <th>Monto total</th>
            <th>Productos</th>
            <th>Descripción</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_monto = 0;
            $total_saldo = 0;
        @endphp
        @foreach ($ingreso_productos as $value)
            <tr class="fila" data-id="{{ $value->id }}">
                <td>{{ $value->nro_lote }}</td>
                <td data-val="{{ $value->precio_total }}" data-pago="0">{{ $value->precio_total }}</td>
                <td>
                    <ul>
                        @foreach ($value->detalle_ingresos as $di)
                            <li>{{ $di->producto->nombre }} (<strong>{{ $di->kilos }} kg -
                                    {{ $di->cantidad }}</strong>)</li>
                        @endforeach
                    </ul>
                </td>
                <td>{{ $value->descripcion }}</td>
                <td data-val="{{ $value->saldo }}" data-pago="0">{{ $value->saldo }}</td>
            </tr>
            @php
                $total_monto += (float) $value->precio_total;
                $total_saldo += (float) $value->saldo;
            @endphp
        @endforeach
        <tr class="bg-primary font-weight-bold text-lg">
            <td>TOTAL</td>
            <td id="total_monto">{{ number_format($total_monto, 2, '.', '') }}</td>
            <td></td>
            <td></td>
            <td id="total_saldo">{{ number_format($total_saldo, 2, '.', '') }}</td>
        </tr>
    </tbody>
</table>
