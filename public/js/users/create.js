let select_tipo = $('#select_tipo');
let select_caja = $('#select_caja');

$(document).ready(function () {
    select_tipo.change(getCajas);
});

function getCajas() {
    let tipo = select_tipo.val();
    if (tipo == 'CAJA') {
        select_caja.closest('.col-md-4').removeClass('oculto');
        select_caja.prop('required', true);
    } else {
        select_caja.closest('.col-md-4').addClass('oculto');
        select_caja.removeAttr('required');
    }
}
