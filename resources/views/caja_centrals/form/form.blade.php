<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha*</label>
            {{ Form::date('fecha', isset($caja_central) ? $caja_central->fecha : date('Y-m-d'), ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Monto*</label>
            {{ Form::number('monto', null, ['class' => 'form-control', 'required', 'step' => '0.01']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Concepto*</label>
            {{ Form::select('concepto_id', $array_conceptos, null, ['class' => 'form-control', 'required', 'id' => 'concepto_id']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="check_lote">Seleccionar lote <input type="checkbox" name="check_lote" value="si"
                    {{ isset($caja_central) && $caja_central->ingreso_producto_id != 0 ? 'checked' : '' }}
                    id="check_lote"></label>
            {{ Form::select('ingreso_producto_id', $array_lotes, null, ['class' => 'form-control contenedor_lote', 'id' => 'ingreso_producto_id', 'required']) }}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Descripción*</label>
            {{ Form::textarea('descripcion', null, ['class' => 'form-control', 'required', 'rows' => '2']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Tipo de movimiento*</label>
            @if (isset($caja_central))
                {{ Form::text('ti', $caja_central->tipo, ['class' => 'form-control', 'required', 'readonly']) }}
            @else
                {{ Form::select('tipo', ['' => 'Seleccione...', 'INGRESO' => 'INGRESO', 'EGRESO' => 'EGRESO'], null, ['class' => 'form-control', 'required']) }}
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Transacción:</label>
            <select name="tipo_transaccion" id="tipo_transaccion" class="form-control" required>
                <option value="BANCO">BANCO</option>
                <option value="CAJA">CAJA</option>
            </select>
        </div>
    </div>
</div>
