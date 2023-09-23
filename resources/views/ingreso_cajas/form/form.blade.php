@if (isset($ingreso_caja))
    <input type="hidden" name="caja_id" value="{{ $ingreso_caja->caja->id }}">
@elseif(isset($caja))
    <input type="hidden" name="caja_id" value="{{ $caja->id }}">
@endif

<input type="hidden" name="registro_id" value="0">
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha*</label>
            {{ Form::date('fecha', isset($ingreso_caja) ? $ingreso_caja->fecha : date('Y-m-d'), ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Monto*</label>
            {{ Form::number('monto_total', null, ['class' => 'form-control', 'required', 'step' => '0.01']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Concepto*</label>
            {{ Form::select('concepto_id', $array_conceptos, null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Descripción*</label>
            {{ Form::textarea('tipo', null, ['class' => 'form-control', 'required', 'rows' => '2']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Tipo de movimiento*</label>
            @if (isset($ingreso_caja))
                {{ Form::text('ti', $ingreso_caja->tipo_movimiento, ['class' => 'form-control', 'required', 'readonly']) }}
            @else
                {{ Form::select('tipo_movimiento', ['' => 'Seleccione...', 'INGRESO' => 'INGRESO', 'EGRESO' => 'EGRESO'], null, ['class' => 'form-control', 'required', 'id' => 'tipo_movimiento']) }}
            @endif
        </div>
    </div>
    {{-- @if (isset($ingreso_caja) && $ingreso_caja->tipo_movimiento == 'EGRESO')
        <div class="col-md-4">
            <label>Caja central/Gasto</label>
            {{ Form::text('sw', $ingreso_caja->sw_egreso, ['class' => 'form-control', 'required', 'readonly']) }}
        </div>
    @else
        <div class="col-md-4 oculto">
            <label>Caja central/Gasto</label>
            {{ Form::select('sw_egreso', ['' => 'Seleccione...', 'GASTO' => 'GASTO', 'EGRESO A CAJA CENTRAL' => 'EGRESO A CAJA CENTRAL'], null, ['class' => 'form-control', 'required', 'id' => 'sw_egreso']) }}
        </div>
    @endif --}}
</div>
