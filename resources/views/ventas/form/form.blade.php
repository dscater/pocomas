<div class="row group_info">
    <div class="col-md-4">
        <div class="form-group">
            <label>C.I.*</label>
            <div class="input-group">
                {{ Form::text('nit', null, ['class' => 'form-control', 'required', 'id' => 'input_nit']) }}
                <div class="input-group-prepend">
                    <button type="button" id="btnBuscaCliente" class="btn btn-default"><i
                            class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Cliente* <span id="nuevoCliente" class="ir-evaluacion" data-toggle="modal"
                    data-target="#m_nuevo_cliente"><i class="fa fa-plus" data-toggle="tooltip"
                        title="Registrar Cliente"></i></span></label>
            {{-- <input type="text" class="form-control" value="" id="input_cliente" readonly required> --}}
            {{-- <input type="hidden" name="cliente_id" id="cliente_id" value="" required> --}}
            {{ Form::select('cliente_id', $array_clientes, null, ['class' => 'form-control select2', 'id' => 'cliente_id']) }}
            {{-- <select name="cliente_id" id="cliente_id" class="form-control select2"></select> --}}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Tipo de Venta*</label>
            {{ Form::select('tipo_venta', ['' => 'Seleccione...', 'AL CONTADO' => 'AL CONTADO', 'POR COBRAR' => 'POR COBRAR', 'ANTICIPOS' => 'ANTICIPOS', 'BANCO' => 'BANCO'], null, ['class' => 'form-control', 'required', 'id' => 'tipo_venta']) }}
        </div>
    </div>
    {{-- <div class="col-md-4 contenedor_concepto oculto">
        <div class="form-group">
            <label>Concepto*</label>
            {{ Form::select('concepto_id', $array_conceptos, null, ['class' => 'form-control', 'id' => 'concepto_id']) }}
        </div>
    </div> --}}
</div>
<div class="row group_producto">
    <div class="col-md-4">
        <div class="form-group">
            <label>Producto*</label>
            {{ Form::select('producto_id', $array_productos, null, ['class' => 'form-control select2', 'id' => 'select_producto']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label id="lbl_cantidad">Cantidad*</label>
            {{ Form::number('c', null, ['class' => 'form-control', 'id' => 'input_cantidad', 'step' => '0.01']) }}
        </div>
    </div>
    {{-- <div class="col-md-4">
        <div class="form-group">
            <label>Seleccionar stock*</label>
            {{ Form::select('prioridad_stock', ['NORMAL' => 'NORMAL', 'PRINCIPAL' => 'PRINCIPAL'], 'PRINCIPAL', ['class' => 'form-control', 'id' => 'prioridad_stock']) }}
        </div>
    </div> --}}
    <div class="col-md-4">
        <div class="form-group">
            <label class="oculto_cel">&nbsp;</label>
            <button type="button" class="btn btn-primary btn-block" id="btnAgregarProducto"><i class="fa fa-plus"></i>
                Agregar</button>
        </div>
    </div>
</div>

<div class="row group_carrito mt-2">
    <div class="col-md-12 p-0">
        <table class="table table-bordered tabla_venta">
            <thead>
                <tr class="bg-red">
                    <th>Nº</th>
                    <th>Producto</th>
                    <th>C/U</th>
                    <th width="120px">Cantidad</th>
                    <th width="120px">Total S/D</th>
                    <th width="120px">Descuento</th>
                    <th width="120px">Total</th>
                    <th width="40px">Quitar</th>
                </tr>
            </thead>
            <tbody id="contenedorCarrito">

            </tbody>
            <tfoot>
                <tr class="totales">
                    <td colspan="3" class="text-right">
                        <span class="mr-3">TOTAL</span>
                    </td>
                    <td data-col="Total Cantidad: ">
                        <input type="number" name="ct" class="form-control" value="" readonly required>
                        <input type="hidden" name="cantidad_total" value="" id="input_cantidad_total" required>
                    </td>
                    <td colspan="2"></td>
                    <td data-col="Total Monto:">
                        <input type="number" name="mt" class="form-control" value="" readonly required>
                        <input type="hidden" name="monto_total" value="" id="input_monto_total" required>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" class="font-weight-bold text-right p-0 pr-2">MONTO RECIBIDO</td>
                    <td class="p-0" data-col="Monto recibido: "><input type="number" class="form-control mb-0"
                            value="" name="monto_recibido" id="monto_recibido"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" class="font-weight-bold text-right p-0 pr-2">CAMBIO</td>
                    <td class="p-0" data-col="Cambio: "><input type="number" class="form-control mb-0"
                            value="0.00" readonly name="monto_cambio" id="monto_cambio"></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-md-4 contenedor_anticipo oculto">
        <div class="form-group">
            <label>Monto Anticipo*</label>
            {{ Form::number('anticipo', null, ['class' => 'form-control', 'id' => 'anticipo', 'step' => '0.01']) }}
        </div>
    </div>
    <div class="col-md-4 contenedor_anticipo oculto">
        <div class="form-group">
            <label>Saldo*</label>
            {{ Form::number('saldo', null, ['class' => 'form-control', 'id' => 'saldo_anticipo', 'step' => '0.01', 'readonly']) }}
        </div>
    </div>
</div>

<input type="hidden" value="{{ route('clientes.nuevo_cliente') }}" id="urlNuevoCliente">
