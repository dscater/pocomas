<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label>Lote*</label>
            {{ Form::select('ingreso_producto_id', $array_lotes, isset($merma) ? $merma->ingreso_producto->id : null, ['class' => 'form-control select2', 'id' => 'ingreso_producto_id']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Producto*</label>
            {{ Form::select('producto_id', [], null, ['class' => 'form-control select2', 'id' => 'producto_id', 'required']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Cantidad de kilos*</label>
            {{ Form::number('cantidad_kilos', null, ['class' => 'form-control', 'required', 'step' => '0.01', 'min' => '0', 'id' => 'cantidad_kilos']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Cantidad de cerdo*</label>
            {{ Form::number('cantidad', null, ['class' => 'form-control', 'required', 'step' => '0.01', 'min' => '0', 'id' => 'cantidad']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Fecha*</label>
            {{ Form::date('fecha', isset($merma) ? $merma->fecha : date('Y-m-d'), ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>
