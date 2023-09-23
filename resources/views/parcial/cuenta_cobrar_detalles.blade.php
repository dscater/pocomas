<strong><i>Ingresa el monto a cancelar por producto en la siguiente tabla:</i></strong>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Producto</th>
            <th width="5px">Cant.</th>
            <th>Monto</th>
            <th>Cancelado</th>
            <th>Saldo</th>
            <th width="90px">Monto a cancelar</th>
        </tr>
    </thead>
    <tbody>
        @php
            $cont = 1;
        @endphp
        @foreach ($cuenta_cobrar->cuenta_cobrar_detalles as $value)
            @if ($value->saldo > 0)
                <tr data-id="{{ $value->id }}">
                    <td>{{ $value->venta_detalle->producto->nombre }}</td>
                    <td>{{ $value->venta_detalle->cantidad }}</td>
                    <td>{{ $value->monto }}</td>
                    <td>{{ $value->cancelado }}</td>
                    <td>{{ $value->saldo }}</td>
                    <td class="td_input"><input type="number" value="0" class="form-control"></td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
