<table class="table table-bordered">
    <thead>
        <tr class="bg-danger">
            <th width="100px">Nro. Orden</th>
            <th>Monto total</th>
            <th>Saldo</th>
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
                <td data-val="{{ $value->monto_deuda }}" data-pago="0">{{ $value->monto_deuda }}</td>
                <td data-val="{{ $value->saldo }}" data-pago="0">{{ $value->saldo }}</td>
            </tr>
            @php
                $total_monto += (float) $value->monto_deuda;
                $total_saldo += (float) $value->saldo;
            @endphp
        @endforeach
        <tr class="bg-primary font-weight-bold text-lg">
            <td>TOTAL</td>
            <td id="total_monto">{{ number_format($total_monto, 2, '.', '') }}</td>
            <td id="total_saldo">{{ number_format($total_saldo, 2, '.', '') }}</td>
        </tr>
    </tbody>
</table>
