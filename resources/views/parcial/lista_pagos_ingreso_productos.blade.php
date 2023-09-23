<table class="table table-bordered bg-dark">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Monto</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
        @if (count($cuenta_pagar_detalles) > 0)
            @php
                $total = 0;
            @endphp
            @foreach ($cuenta_pagar_detalles as $cpd)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($cpd->fecha)) }}</td>
                    <td>{{ $cpd->monto }}</td>
                    <td>{{ $cpd->descripcion }}</td>
                </tr>
                @php
                    $total += (float) $cpd->monto;
                @endphp
            @endforeach
            <tr>
                <td class="font-weight-bold text-md">TOTAL</td>
                <td class="font-weight-bold text-md">{{ number_format($total, 2) }}</td>
                <td></td>
            </tr>
        @else
            <tr>
                <td colspan="3" class="text-center"><i>SIN PAGOS AÚN</i></td>
            </tr>
        @endif
    </tbody>
</table>
