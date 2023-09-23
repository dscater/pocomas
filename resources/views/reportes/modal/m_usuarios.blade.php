<div class="modal fade" id="m_usuarios">
    <div class="modal-dialog">
      <div class="modal-content  bg-sucess">
        <div class="modal-header">
            <h4 class="modal-title">Lista de usuarios</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            {!! Form::open(['route'=>'reportes.usuarios','method'=>'get','target'=>'_blank','id'=>'formUsuarios']) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Filtro:</label>
                            <select class="form-control" name="filtro" id="filtro">
                                <option value="todos">Todos</option>
                                <option value="tipo">Tipo de Usuario</option>
                            </select>
                        </div>
                    </div>
    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Seleccione el tipo de usuario:</label>
                            <select class="form-control" name="tipo" id="tipo">
                                <option value="todos">Todos</option>
                                <option value="ADMINISTRADOR">Administrador</option>
                                <option value="AUXILIAR">Auxiliar</option>
                                <option value="CAJA">Caja</option>
                            </select>
                        </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-info" id="btnUsuarios">Generar reporte</button>
            {!! Form::close() !!}
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
   <!-- /.modal -->
