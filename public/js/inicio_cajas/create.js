let urlUltimoMontoCaja = $('#urlUltimoMontoCaja').val();
let monto = $('#monto');
let select_caja = $('#select_caja');

$(document).ready(function () {
    select_caja.change(getUltimoMontoCaja);
});

function getUltimoMontoCaja() {
    $.ajax({
        type: "GET",
        url: urlUltimoMontoCaja,
        data: {
            caja_id: select_caja.val()
        },
        dataType: "json",
        success: function (response) {
            monto.val(response.monto);
        }
    });
}
