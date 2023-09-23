<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Nombre(s)*</label>
            {{ Form::text('nombre', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Ap. Paterno*</label>
            {{ Form::text('paterno', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Ap. Materno</label>
            {{ Form::text('materno', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label>C.I.*</label>
            {{ Form::number('ci', null, ['class' => 'form-control', 'required']) }}
            @if ($errors->has('ci'))
                <span class="invalid-feedback text-danger" style="display:block" role="alert">
                    <strong>{{ $errors->first('ci') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Expedido*</label>
            {{ Form::select('ci_exp',[
                '' => 'Seleccione...',
                'LP' => 'LA PAZ',
                'CB' => 'COCHABAMBA',
                'SC' => 'SANTA CRUZ',
                'PT' => 'POTOSI',
                'OR' => 'ORURO',
                'CH' => 'CHUQUISACA',
                'TJ' => 'TARIJA',
                'BN' => 'BENI',
                'PD' => 'PANDO',
            ],null,['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Sexo*</label>
            {{ Form::select('sexo',[
                '' => 'Seleccione...',
                'HOMBRE' => 'HOMBRE',
                'MUJER' => 'MUJER',
                'OTRO' => 'OTRO',
            ],null,['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Dirección*</label>
            {{Form::text('dir',null,['class'=>'form-control','required'])}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label>Teléfono</label>
            {{ Form::text('fono', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Celular*</label>
            {{ Form::text('cel', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Foto</label>
            <input type="file" name="foto" class="form-control">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha de Ingreso*</label>
            {{Form::date('fecha_ingreso',isset($usuario)? $usuario->fecha_ingreso:date('Y-m-d'),['class'=>'form-control','required'])}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Tipo de Usuario*</label>
            {{ Form::select('tipo',[
                '' => 'Seleccione...',
                'ADMINISTRADOR' => 'ADMINISTRADOR',
                'SUPERVISOR' => 'SUPERVISOR',
                'CAJA' => 'CAJA',
            ],isset($usuario)? $usuario->user->tipo:null,['class' => 'form-control', 'required','id'=>'select_tipo']) }}
        </div>
    </div>

    @php
        $oculto = 'oculto';
        $required = '';
        if(isset($usuario)){
            if($usuario->user->tipo == 'CAJA'){
                $oculto = '';
                $required = 'required';
            }
        }
    @endphp
    
    <div class="col-md-4 {{$oculto}}">
        <div class="form-group">
            <label>Seleccione Caja*</label>
            @if(isset($usuario) &&  $usuario->user->caja)
            {{ Form::select('caja_id',$array_cajas,isset($usuario)? $usuario->user->caja->caja_id:null,['class' => 'form-control','id'=>'select_caja',$required]) }}
            @else
            {{ Form::select('caja_id',$array_cajas,null,['class' => 'form-control','id'=>'select_caja',$required]) }}
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Estado*</label>
            {{ Form::select('estado',[
                '' => 'Seleccione...',
                'ACTIVO' => 'ACTIVO',
                'INACTIVO' => 'INACTIVO',
            ],isset($usuario)? $usuario->user->estado:null,['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>
