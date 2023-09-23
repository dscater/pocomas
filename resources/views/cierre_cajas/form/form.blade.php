<input type="hidden" name="monto_total" value="{{ $monto }}" id="input_monto">
<input type="hidden" name="fecha_cierre" value="{{ date('Y-m-d') }}">
<input type="hidden" name="fecha_registro" value="{{ date('Y-m-d') }}">
<input type="hidden" name="estado" value="1">
<input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
@if (Auth::user()->tipo == 'CAJA')
    <input type="hidden" name="caja_id" value="{{ Auth::user()->caja->caja_id }}">
@else
    <input type="hidden" value="{{ date('Y-m-d') }}" id="input_fecha">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Seleccione Caja*</label>
                {{ Form::select('caja_id', $array_cajas, null, ['class' => 'form-control', 'required', 'id' => 'select_caja']) }}
            </div>
        </div>
    </div>
@endif
<div class="row">
    <div class="col-md-4">
        <br>
        <div id="contendor_monto">
            {{ date('d/m/Y') }}
            <div class="alert alert-danger font-weight-bold text-xl">
                TOTAL: <span id="span_monto">{{ $monto }} Bs.</span>
            </div>
            <div class="alert alert-primary font-weight-bold text-md">
                BANCO: <span id="span_monto">{{ $monto_banco }} Bs.</span>
            </div>
            <div class="alert bg-orange font-weight-bold text-md">
                CAJA: <span id="span_monto">{{ $monto_otros }} Bs.</span>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label>Descripción</label>
            {{ Form::textarea('descripcion', null, ['class' => 'form-control', 'rows' => '2']) }}
        </div>
    </div>
</div>
