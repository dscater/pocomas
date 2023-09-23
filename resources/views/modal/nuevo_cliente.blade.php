<div class="modal fade" id="m_nuevo_cliente">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="txtTituloEliminar">Registrar Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="formNuevoCliente">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nombre Completo*</label>
                                {{ Form::text('nombre', null, ['class' => 'form-control', 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>C.I./NIT*</label>
                                {{ Form::number('ci', null, ['class' => 'form-control', 'required', 'step' => '1', 'min' => '0','id'=>'m_ci_cliente']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                {{ Form::email('email', null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Celular*</label>
                                {{ Form::text('celular', null, ['class' => 'form-control', 'required']) }}
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnRegistraCliente">Registrar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
