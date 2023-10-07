@if (count($detalle_ingresos) > 0)
    @foreach ($detalle_ingresos as $di)
        <tr class="fila existe" data-id="{{ $di->id }}">
            <td>#</td>
            <td><span>{{ $di->producto->nombre }}</span></td>
            <td><span>{{ $di->kilos }}</span></td>
            <td><span>{{ $di->cantidad }}</span></td>
            <td class="accion">
                @if (!$di->ingreso_producto->existe_ventas && !$di->ingreso_producto->existe_pagos)
                    <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                @endif
            </td>
        </tr>
    @endforeach
@endif
