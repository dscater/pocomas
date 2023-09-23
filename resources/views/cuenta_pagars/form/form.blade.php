@if (isset($cuenta_pagar))
    <input type="hidden" id="edit" value="{{ $cuenta_pagar->saldo }}">
@else
    <input type="hidden" id="edit" value="0">
@endif
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Seleccionar proveedor</label>
            {{ Form::select('proveedor_id', $array_proveedors, null, ['class' => 'form-control', 'required', 'id' => 'proveedor_id']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h5>Listado de cuentas por pagar</h5>
        <div id="contenedorDetalle">

        </div>
    </div>
</div>
<hr>
<h4 class="text-center font-weight-bold">Registrar nuevo pago</h4>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Monto total a cancelar*</label>
            {{ Form::number('monto_total', null, ['class' => 'form-control', 'required', 'id' => 'i_total_cancelar']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Saldo pendiente*</label>
            {{ Form::number('saldo', isset($cuenta_pagar) ? null : 0, ['class' => 'form-control', 'required', 'id' => 'i_saldo', 'readonly']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Tipo de pago:</label>
            <select name="tipo_pago" id="tipo_pago" class="form-control" required>
                <option value="BANCO">BANCO</option>
                <option value="CAJA">CAJA</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Descripción</label>
            {{ Form::textarea('descripcion', null, ['class' => 'form-control', 'rows' => '2', 'id' => 'descripcion']) }}
        </div>
    </div>
</div>
