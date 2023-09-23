<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Concepto*</label>
            {{ Form::text('nombre', null, ['class' => 'form-control', 'required']) }}
            @if ($errors->has('nombre'))
                <span class="invalid-feedback text-danger" style="display:block" role="alert">
                    <strong>{{ $errors->first('nombre') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
