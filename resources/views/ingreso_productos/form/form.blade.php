<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>Nro. de lote*</label>
                        <select name="ingreso_producto_id" id="ingreso_producto_id" class="form-control select2">
                            @if (count($ingreso_productos) > 0 || count($ingreso_productos_vacios) > 0)
                                <option value="">- Seleccione -</option>
                                @if (count($ingreso_productos) > 0)
                                    <optgroup label="Nuevos lotes">
                                        @foreach ($ingreso_productos as $ip)
                                            <option value="{{ $ip->id }}">{{ $ip->nro_lote }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if (count($ingreso_productos_vacios) > 0)
                                    <optgroup label="Lotes anteriores">
                                        @foreach ($ingreso_productos_vacios as $ip)
                                            <option value="{{ $ip->id }}">{{ $ip->nro_lote }}</option>
                                        @endforeach

                                    </optgroup>
                                @endif
                            @else
                                <option value="">- No se encontrarón lotes registrados -</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-4 col-12 text-center">
                <div class="row">
                    <div class="p-1 col-12 font-weight-bold bg-danger">Producto Principal</div>
                    <div class="p-1 col-12 text-md bg-danger border border-top" id="txtNomPrincipal">- Seleccione un
                        lote -</div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-center">
                <div class="row">
                    <div class="col-12 p-1 font-weight-bold bg-danger">Total del Lote</div>
                    <div class="col-12">
                        <div class="row bg-primary">
                            <div class="col-6 p-1 text-md border border-top"><strong>Kilos: </strong><span
                                    id="txtTotalKilos">0</span>
                            </div>
                            <div class="col-6 p-1 text-md border border-top"><strong>Cantidad: </strong><span
                                    id="txtTotalCantidad">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-center">
                <div class="row">
                    <div class="col-12 p-1 font-weight-bold bg-danger">Disponible para Ingreso</div>
                    <div class="col-12">
                        <div class="row bg-success">
                            <div class="col-6 p-1 text-md border border-top">
                                <strong>Kilos: </strong>
                                <span id="txtSaldoKilos">0</span>
                                <input type="hidden" name="saldo_kilos" id="inputSaldoKilos"value="0">
                            </div>
                            <div class="col-6 p-1 text-md border border-top">
                                <strong>Cantidad: </strong>
                                <span id="txtSaldoCantidad">0</span>
                                <input type="hidden" name="saldo_cantidad" id="inputSaldoCantidad"value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row contenedor_productos">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body bg-gray">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-white">Seleccione Producto*</label>
                            {{ Form::select('producto_id', [], null, ['class' => 'form-control select2', 'id' => 'producto_id']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="text-white">Kilos*</label>
                            {{ Form::number('kilos', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'id' => 'kilos']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="text-white">Cantidad*</label>
                            {{ Form::text('cantidad', null, ['class' => 'form-control', 'id' => 'cantidad']) }}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="button" id="btnAgregar" class="btn btn-sm btn-block btn-flat bg-red m-1"><i
                                class="fa fa-plus"></i> AGREGAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row contenedor_productos">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr class="bg-gray">
                    <th width="20px">#</th>
                    <th>Producto</th>
                    <th>Kilos</th>
                    <th>Cantidad</th>
                    <th width="20px">Acción</th>
                </tr>
            </thead>
            <tbody id="contenedor_filas" class="bg-white">
            </tbody>
            <tfoot>
                <tr class="bg-gray">
                    <th colspan="2" class="text-right text-lg">TOTAL</th>
                    <th id="total_kilos"><span class="text-lg">0</span><input type="hidden" name="total_kilos"></th>
                    <th id="total_cantidad"><span class="text-lg">0</span><input type="hidden" name="total_cantidad">
                    </th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
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
