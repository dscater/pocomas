<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Nro. de lote*</label>
            {{ Form::text('nro_lote', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Proveedor*</label>
            {{ Form::select('proveedor_id', $array_proveedors, null, ['class' => 'form-control select2', 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Precio de Compra*</label>
            {{ Form::number('precio_compra', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'id' => 'precio_compra']) }}
        </div>
    </div>
</div>

<div class="row m-0 border p-3 bg-gray">
    <div class="col-md-6">
        <div class="form-group">
            <label class="text-white">Seleccione Producto*</label>
            {{ Form::select('producto_id', $array_productos, null, ['class' => 'form-control select2', 'id' => 'producto_id']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="text-white">Kilos <span id="medida"></span>*</label>
            {{ Form::number('kilos', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'id' => 'kilos']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="text-white">Cantidad <span id="medida"></span>*</label>
            {{ Form::text('cantidad', null, ['class' => 'form-control', 'id' => 'cantidad']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="text-white">Controlar stock por <span id="medida"></span>*</label>
            {{ Form::select('tipo_control', ['KILOS' => 'KILOS', 'CANTIDAD' => 'CANTIDAD'], null, ['class' => 'form-control', 'id' => 'tipo_control']) }}
        </div>
    </div>
    <div class="col-md-12">
        <button type="button" id="btnAgregar" class="btn btn-sm btn-block btn-flat bg-red m-1"><i
                class="fa fa-plus"></i> AGREGAR</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr class="bg-gray">
                    <th width="20px">#</th>
                    <th>Producto</th>
                    <th>Kilos</th>
                    <th>Cantidad</th>
                    <th>Precio Compra</th>
                    <th width="90px">Control de stock</th>
                    <th width="20px">Acción</th>
                </tr>
            </thead>
            <tbody id="contenedor_filas">
                @if (isset($ingreso_producto))
                    @foreach ($ingreso_producto->detalle_ingresos as $di)
                        <tr class="fila existe" data-id="{{ $di->id }}">
                            <td>#</td>
                            <td><span>{{ $di->producto->nombre }}</span></td>
                            <td><span>{{ $di->kilos }}</span></td>
                            <td><span>{{ $di->cantidad }}</span></td>
                            <td><span>{{ $di->precio_compra }}</span></td>
                            <td><span>{{ $di->tipo_control }}</span></td>
                            <td class="accion"><button type="button" class="btn btn-sm btn-danger"><i
                                        class="fa fa-trash"></i></button></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr class="bg-gray">
                    <th colspan="2" class="text-right text-lg">TOTAL</th>
                    <th id="total_kilos"><span>0</span><input type="hidden" name="total_kilos"></th>
                    <th id="total_cantidad"><span>0</span><input type="hidden" name="total_cantidad"></th>
                    <th id="precio_total" class="text-lg"><span>0.00</span><input type="hidden" name="precio_total">
                    </th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Seleccione*</label>
            {{ Form::select('tipo', ['POR PAGAR' => 'POR PAGAR', 'AL CONTADO' => 'AL CONTADO'], null, ['class' => 'form-control', 'rows' => '2']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Descripción</label>
            {{ Form::textarea('descripcion', null, ['class' => 'form-control', 'rows' => '2']) }}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha de Ingreso*</label>
            {{ Form::date('fecha_ingreso', isset($ingreso_producto) ? $ingreso_producto->fecha_ingreso : date('Y-m-d'), ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>

<div class="row" id="eliminados"></div>

@if ($errors->has('nro_lote'))
    <ul class="error-list">
        @foreach ($errors->get('nro_lote') as $error)
            <li class="error">{{ $error }}</li>
        @endforeach
    </ul>
@endif
