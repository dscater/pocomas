<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Nro. de lote*</label>
            {{ Form::text('nro_lote', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Proveedor*</label>
            {{ Form::select('proveedor_id', $array_proveedors, null, ['class' => 'form-control select2', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Precio de Compra*</label>
            {{ Form::number('precio_compra', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'id' => 'precio_compra']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Seleccione Producto*</label>
            {{ Form::select('producto_id', $array_productos, null, ['class' => 'form-control select2', 'id' => 'producto_id', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Kilos de cerdos*</label>
            {{ Form::number('total_kilos', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'id' => 'kilos', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Cantidad de cerdos*</label>
            {{ Form::text('total_cantidad', null, ['class' => 'form-control', 'id' => 'cantidad']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Seleccione*</label>
            {{ Form::select('tipo', ['POR PAGAR' => 'POR PAGAR', 'AL CONTADO' => 'AL CONTADO'], null, ['class' => 'form-control', 'rows' => '2']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Descripción</label>
            {{ Form::textarea('descripcion', null, ['class' => 'form-control', 'rows' => '2']) }}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha de Ingreso*</label>
            {{ Form::date('fecha_ingreso', isset($ingreso_producto) ? $ingreso_producto->fecha_ingreso : date('Y-m-d'), ['class' => 'form-control', 'required']) }}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Precio total*</label>
            {{ Form::text('precio_total', null, ['class' => 'form-control', 'id' => 'precio_total', 'readonly']) }}
        </div>
    </div>
</div>
