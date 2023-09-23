<div class="modal fade" id="modal_cerrar_caja">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="txtTituloCerrarCaja">Confirmar cierre de caja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="formCerrarCaja">
                    @csrf
                    <p id="mensajeCerrarCaja"></p>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">No, cancelar</button>
                <button type="button" class="btn btn-danger" id="btnCerrarCaja">Si, cerrar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
