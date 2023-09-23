<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Código*</label>
            {{ Form::text('codigo', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label>Nombre Caja*</label>
            {{ Form::text('nombre', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Descripción*</label>
            {{ Form::textarea('descripcion', null, ['class' => 'form-control', 'required','rows'=>'2']) }}
        </div>
    </div>
</div>
