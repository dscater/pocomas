<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Nombre Completo*</label>
            {{ Form::text('nombre', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>C.I./NIT*</label>
            {{ Form::number('ci', null, ['class' => 'form-control', 'required', 'step' => '1', 'min' => '0']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Razón social</label>
            {{ Form::text('razon_social', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Email</label>
            {{ Form::email('email', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Celular*</label>
            {{ Form::text('celular', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>
