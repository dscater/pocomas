<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Seleccione Caja*</label>
            @if(isset($inicio_caja))
            {{ Form::text('caja', $inicio_caja->caja->nombre, ['class' => 'form-control', 'required','readonly']) }}
            @else
            {{ Form::select('caja_id',$array_cajas, null, ['class' => 'form-control select2', 'required','id'=>'select_caja']) }}
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Monto Inicial*</label>
            {{ Form::number('monto_inicial', isset($inicio_caja)? $inicio_caja->monto_inicial:0, ['class' => 'form-control', 'required','step'=>'0.01','min'=>'0','id'=>'monto']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha Inicio*</label>
            {{ Form::date('fecha_inicio', isset($inicio_caja)? $inicio_caja->fecha_inicio:date('Y-m-d'), ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>

<div class="row">   
    <div class="col-md-4">
        <div class="form-group">
            <label>Descripción</label>
            {{ Form::textarea('descripcion', null, ['class' => 'form-control','rows'=>'2']) }}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Usuario Caja*</label>
            {{ Form::select('user_id', $array_users ,isset($inicio_caja)? $inicio_caja->user_id:null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>
{{-- <input type="hidden" id="urlUltimoMontoCaja" value="{{route('cierre_cajas.urlUltimoMontoCaja')}}"> --}}