<div class="row">
    {{-- <div class="col-md-4">
        <div class="form-group">
            <label>Nro. de lote*</label>
            {{ Form::select('ingreso_producto_id', $array_ingreso_productos, null, ['class' => 'form-control', 'required', 'id' => 'ingreso_producto_id']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Seleccionar producto*</label>
            {{ Form::select('detalle_ingreso_id', [], isset($merma) ? $merma->detalle_ingreso_id : null, ['class' => 'form-control', 'required', 'id' => 'detalle_ingreso_id']) }}
        </div>
    </div> --}}
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha*</label>
            {{ Form::date('fecha', isset($merma) ? $merma->fecha : date('Y-m-d'), ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Producto*</label>
            {{ Form::select('producto_id', $array_productos, null, ['class' => 'form-control select2', 'id' => 'select_producto', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Cantidad*</label>
            {{ Form::number('cantidad', null, ['class' => 'form-control', 'required', 'step' => '0.01', 'min' => '0', 'id' => 'cantidad']) }}
        </div>
    </div>
</div>
