<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Nombre del Propietario*</label>
            {{ Form::text('propietario', null, ['class' => 'form-control', 'required']) }}
            @if ($errors->has('propietario'))
                <span class="invalid-feedback text-danger" style="display:block" role="alert">
                    <strong>{{ $errors->first('propietario') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Razón Social*</label>
            {{ Form::text('razon_social', null, ['class' => 'form-control', 'required']) }}
            @if ($errors->has('razon_social'))
                <span class="invalid-feedback text-danger" style="display:block" role="alert">
                    <strong>{{ $errors->first('razon_social') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Teléfono*</label>
            {{ Form::text('fono', null, ['class' => 'form-control', 'required']) }}
            @if ($errors->has('fono'))
                <span class="invalid-feedback text-danger" style="display:block" role="alert">
                    <strong>{{ $errors->first('fono') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Dirección</label>
            {{ Form::text('dir', null, ['class' => 'form-control']) }}
            @if ($errors->has('dir'))
                <span class="invalid-feedback text-danger" style="display:block" role="alert">
                    <strong>{{ $errors->first('dir') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
