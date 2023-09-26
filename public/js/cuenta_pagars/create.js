let proveedor_id = $("#proveedor_id");

let contenedorDetalle = $("#contenedorDetalle");
let i_total_cancelar = $("#i_total_cancelar");
let i_saldo = $("#i_saldo");
let total_monto = 0;
let total_saldo = 0;
let btn_registrar_pago = $("#btn_registrar_pago");
let total_cuentas = 0;
let formCuentaPagar = $("#formCuentaPagar");
$(document).ready(function () {
    validaEnvio();
    proveedor_id.change(getCuentas);
    i_total_cancelar.on("keyup change", function () {
        validaTotalPago();
        validaEnvio();
    });

    $("#descripcion").on("keyup change", function () {
        validaEnvio();
    });

    btn_registrar_pago.click(function (e) {
        e.preventDefault();
        swal.fire({
            title: "Confirmar pago",
            html: `<b>Total cancelar:</b> ${i_total_cancelar.val()}<br><b>Saldo:</b> ${i_saldo.val()}`,
            showCancelButton: true,
            confirmButtonText: "Pagar",
            cancelButtonText: `Cancelar`,
            confirmButtonColor: "#bd2130",
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.value) {
                if (prepararEnvio()) {
                    $.ajax({
                        headers: { "X-CSRF-TOKEN": $("#token").val() },
                        type: "POST",
                        url: formCuentaPagar.attr("action"),
                        data: {
                            tipo_pago: $("#tipo_pago").val(),
                            data: prepararEnvio(),
                            descripcion: $("#descripcion").val(),
                            monto_total: $("#i_total_cancelar").val(),
                        },
                        dataType: "json",
                        success: function (response) {
                            if (response.sw) {
                                swal.fire({
                                    title: "Registro éxitoso",
                                    icon: "success",
                                    text: `El registro se realizó correctamente`,
                                    showConfirmButton: false,
                                    // confirmButtonText: "Aceptar",
                                    // confirmButtonColor: "#bd2130",
                                });
                                setTimeout(function () {
                                    redirigirPagina(response.url_lista);
                                }, 1500);
                            } else {
                                swal.fire({
                                    title: "Error",
                                    icon: "error",
                                    text: `${response.message}`,
                                    confirmButtonText: "Aceptar",
                                    confirmButtonColor: "#bd2130",
                                });
                            }
                            getCuentas();
                            i_total_cancelar.val("");
                            $("#descripcion").val("");
                        },
                    });
                }
            }
        });
    });
});

function redirigirPagina(url) {
    window.location = url;
}

function getCuentas() {
    if (proveedor_id.val() != "") {
        $.ajax({
            type: "GET",
            url: $("#urlGetCuentas").val(),
            data: {
                proveedor_id: proveedor_id.val(),
            },
            dataType: "json",
            success: function (response) {
                total_cuentas = response.total_cuentas;
                if (response.html != "" && response.total_cuentas > 0) {
                    contenedorDetalle.html(response.html);
                    getTotales();
                } else {
                    contenedorDetalle.html(
                        '<h4 class="text-center">No se encontrarón registros</h4>'
                    );
                }
            },
        });
    } else {
        contenedorDetalle.html("");
    }
}

function validaEnvio() {
    let valor = i_total_cancelar.val();
    if (valor != "" && valor != 0 && total_cuentas > 0) {
        btn_registrar_pago.removeAttr("disabled");
    } else {
        btn_registrar_pago.prop("disabled", true);
    }
}

function getTotales() {
    total_monto = parseFloat($("td#total_monto").text());
    total_saldo = parseFloat($("td#total_saldo").text());
    i_saldo.val(total_saldo);
}

function sumaSaldos() {
    let filas = contenedorDetalle.find(".fila");
    let s_total_saldos = 0;
    filas.each(function () {
        let saldo = parseFloat($(this).children("td").eq(4).text());
        s_total_saldos += saldo;
    });
    $("td#total_saldo").text(s_total_saldos.toFixed(2));
    i_saldo.val(s_total_saldos);
}

function validaTotalPago() {
    let valor = i_total_cancelar.val();
    if (parseFloat(valor) > total_saldo) {
        swal.fire({
            title: "Error",
            icon: "error",
            text: `El monto no puede ser mayor a: ${total_saldo}`,
            confirmButtonText: "Aceptar",
            confirmButtonColor: "#bd2130",
        });
        i_total_cancelar.val(total_saldo);
    }
    nuevosSaldos();
}

function nuevosSaldos() {
    let filas = contenedorDetalle.find(".fila");
    let monto_pagar = 0;
    if (
        i_total_cancelar.val() != "" &&
        parseFloat(i_total_cancelar.val()) > 0
    ) {
        monto_pagar = parseFloat(i_total_cancelar.val());
    }
    filas.each(function () {
        let saldo = parseFloat($(this).children("td").eq(4).attr("data-val"));
        let nuevo_saldo = 0;
        if (saldo > monto_pagar) {
            nuevo_saldo = saldo - monto_pagar;
            monto_pagar = 0;
        } else {
            monto_pagar = monto_pagar - saldo;
        }
        $(this).children("td").eq(4).text(nuevo_saldo.toFixed(2));
    });
    sumaSaldos();
}

function prepararEnvio() {
    let filas = contenedorDetalle.find(".fila");
    let data = [];
    filas.each(function () {
        let saldo = parseFloat($(this).children("td").eq(4).attr("data-val"));
        let nuevo_saldo = parseFloat($(this).children("td").eq(4).text());
        data.push({
            id: $(this).attr("data-id"),
            saldo: saldo,
            nuevo_saldo: nuevo_saldo,
        });
    });

    return data;
}
