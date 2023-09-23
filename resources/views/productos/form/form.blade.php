<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Código*</label>
            {{ Form::text('codigo', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label>Nombre Producto*</label>
            {{ Form::text('nombre', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Descripción</label>
            {{ Form::textarea('descripcion', null, ['class' => 'form-control', 'rows' => '2']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Precio de Venta*</label>
            {{ Form::number('precio', null, ['class' => 'form-control', 'required', 'step' => '0.01', 'min' => '0']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Stock Mínimo*</label>
            {{ Form::number('stock_minimo', null, ['class' => 'form-control', 'required', 'step' => '0.01', 'min' => '0']) }}
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
            <label>Estado*</label>
            {{ Form::select(
                'estado',
                [
                    '' => 'Seleccione...',
                    'ACTIVO' => 'ACTIVO',
                    'INACTIVO' => 'INACTIVO',
                ],
                null,
                ['class' => 'form-control', 'required'],
            ) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Prioridad*</label>
            {{ Form::select(
                'prioridad',
                [
                    '' => 'Seleccione...',
                    'NORMAL' => 'NORMAL',
                    'PRINCIPAL' => 'PRINCIPAL',
                    'DEL PRINCIPAL' => 'DEL PRINCIPAL',
                ],
                null,
                ['class' => 'form-control', 'required'],
            ) }}
        </div>
    </div>
</div>
