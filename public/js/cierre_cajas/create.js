let select_caja = $('#select_caja');
let btnRegistraCierre = $('#btnRegistraCierre');
let input_fecha = $('#input_fecha');
let input_monto = $('#input_monto');
let span_monto = $('#span_monto');

$(document).ready(function () {
    habilitaButton();
    select_caja.change(function () {
        habilitaButton();
    });
});

function habilitaButton() {
    if (select_caja.val() != '') {
        ultimoMontoCaja();
    } else {
        span_monto.text('No se seleccionó una caja');
        btnRegistraCierre.prop('disabled', true);
    }
}

function ultimoMontoCaja() {
    span_monto.text('Cargando...');
    $.ajax({
        type: "GET",
        url: $('#urlUltimoMontoCaja').val(),
        data: {
            caja_id: select_caja.val(),
            fecha: input_fecha.val()
        },
        dataType: "json",
        success: function (response) {
            btnRegistraCierre.removeAttr('disabled');
            input_monto.val(response.monto);
            span_monto.text(response.monto + ' Bs.');
        }
    });
}
