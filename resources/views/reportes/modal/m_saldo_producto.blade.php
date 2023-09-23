<div class="modal fade" id="m_saldo_producto">
    <div class="modal-dialog">
        {!! Form::open([
            'route' => 'reportes.saldo_producto',
            'method' => 'get',
            'target' => '_blank',
        ]) !!}
        <div class="modal-content  bg-sucess">
            <div class="modal-header">
                <h4 class="modal-title">Saldo por producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Fecha:</label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" class="form-control"
                                required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-info">Generar reporte</button>
            </div>
        </div>
        <!-- /.modal-content -->
        {!! Form::close() !!}
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
