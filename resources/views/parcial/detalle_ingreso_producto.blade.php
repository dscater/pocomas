<table class="table table-bordered bg-dark">
    <tbody>
        <tr>
            <td width="130px">Nro. lote:</td>
            <td>{{ $ingreso_producto->nro_lote }}</td>
        </tr>
        <tr>
            <td>Proveedor:</td>
            <td>{{ $ingreso_producto->proveedor->razon_social }}</td>
        </tr>
        <tr>
            <td>Descripción:</td>
            <td>{{ $ingreso_producto->descripcion }}</td>
        </tr>
        <tr>
            <td>Fecha de ingreso:</td>
            <td>{{ date('d/m/Y', strtotime($ingreso_producto->fecha_ingreso)) }}</td>
        </tr>
        <tr>
            <td>Saldo:</td>
            <td><span class="badge badge-primary text-md">{{ $ingreso_producto->saldo }}</span></td>
        </tr>
        <tr>
            <td>Total:</td>
            <td><span class="badge badge-danger text-md">{{ $ingreso_producto->precio_total }}</span></td>
        </tr>
    </tbody>
</table>
