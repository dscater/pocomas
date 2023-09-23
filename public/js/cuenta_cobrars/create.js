let select_cliente = $("#select_cliente");
let contenedor_cuentas = $("#contenedor_cuentas");
let formulario_pago = $("#formulario_pago");
let i_total_cancelar = $("#i_total_cancelar");
let i_saldo = $("#i_saldo");
let btn_registrar_pago = $("#btn_registrar_pago");
let elemento = null;

let total_monto = 0;
let total_saldo = 0;

$(document).ready(function () {
    validaEnvio();
    select_cliente.change(function () {
        getCuentasCliente();
    });

    i_total_cancelar.on("change keyup", function () {
        validaTotalPago();
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
                    if ($("#tipo_cobro").val().trim() != "") {
                        $.ajax({
                            headers: { "X-CSRF-TOKEN": $("#token").val() },
                            type: "POST",
                            url: $("#urlRegistraPago").val(),
                            data: {
                                data: prepararEnvio(),
                                observacion: $("#observacion").val(),
                                tipo_cobro: $("#tipo_cobro").val(),
                                caja_id: $("#caja_id").val(),
                                cliente_id: select_cliente.val(),
                                total_cancelado: $("#i_total_cancelar").val(),
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.sw) {
                                    swal.fire({
                                        title: "Registro éxitoso",
                                        icon: "success",
                                        text: `El registro se realizó correctamente`,
                                        confirmButtonText: "Aceptar",
                                        confirmButtonColor: "#bd2130",
                                    });
                                    i_total_cancelar.val("");
                                    redirigirPagina(response.url_comprobante);
                                    getCuentasCliente();
                                } else {
                                    swal.fire({
                                        title: "Error",
                                        icon: "error",
                                        text: `Ocurrió el sgte. error: ${response.message}`,
                                        confirmButtonText: "Aceptar",
                                        confirmButtonColor: "#bd2130",
                                    });
                                }
                            },
                            error: function (e) {
                                let mensaje = "";
                                // console.log(e.responseJSON.errors);
                                if (e.responseJSON.errors) {
                                    errores = e.responseJSON.errors;
                                    for (const propiedad in errores) {
                                        const mensajesDeError =
                                            errores[propiedad];
                                        console.log(propiedad + ":");
                                        for (const msj of mensajesDeError) {
                                            // console.log(msj);
                                            mensaje += `${msj}<br>`;
                                        }
                                    }
                                    swal.fire({
                                        title: "Error",
                                        icon: "error",
                                        html: `${mensaje}`,
                                        confirmButtonText: "Aceptar",
                                        confirmButtonColor: "#bd2130",
                                    });
                                } else {
                                    swal.fire({
                                        title: "Error",
                                        icon: "error",
                                        text: `${e.responseJSON.message}`,
                                        confirmButtonText: "Aceptar",
                                        confirmButtonColor: "#bd2130",
                                    });
                                }
                            },
                        });
                    } else {
                        swal.fire({
                            title: "Error",
                            icon: "error",
                            text: `Debes seleccionar el tipo de cobro: BANCO/CAJA`,
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: "#bd2130",
                        });
                    }
                }
            }
        });
    });
});

function redirigirPagina(url) {
    window.location = url;
}

function getCuentasCliente() {
    contenedor_cuentas.html("Cargando...");
    formulario_pago.addClass("oculto");
    if (select_cliente.val() != "") {
        $.ajax({
            type: "GET",
            url: $("#urlCuentasClientes").val(),
            data: {
                cliente_id: select_cliente.val(),
            },
            dataType: "json",
            success: function (response) {
                total_cuentas = response.total_cuentas;
                if (response.html != "" && response.total_cuentas > 0) {
                    formulario_pago.removeClass("oculto");
                    contenedor_cuentas.html(response.html);
                    getTotales();
                } else {
                    contenedor_cuentas.html(
                        '<h4 class="text-center">No se encontrarón registros</h4>'
                    );
                }
            },
        });
    } else {
        contenedor_cuentas.html("");
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
    let filas = contenedor_cuentas.find(".fila");
    let s_total_saldos = 0;
    filas.each(function () {
        let saldo = parseFloat($(this).children("td").eq(2).text());
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
    let filas = contenedor_cuentas.find(".fila");
    let monto_pagar = 0;
    if (
        i_total_cancelar.val() != "" &&
        parseFloat(i_total_cancelar.val()) > 0
    ) {
        monto_pagar = parseFloat(i_total_cancelar.val());
    }
    filas.each(function () {
        let saldo = parseFloat($(this).children("td").eq(2).attr("data-val"));
        let nuevo_saldo = 0;
        if (saldo > monto_pagar) {
            nuevo_saldo = saldo - monto_pagar;
            monto_pagar = 0;
        } else {
            monto_pagar = monto_pagar - saldo;
        }

        $(this).children("td").eq(2).text(nuevo_saldo.toFixed(2));
    });
    sumaSaldos();
}

function prepararEnvio() {
    let filas = contenedor_cuentas.find(".fila");
    let data = [];
    filas.each(function () {
        let saldo = parseFloat($(this).children("td").eq(2).attr("data-val"));
        let nuevo_saldo = parseFloat($(this).children("td").eq(2).text());
        data.push({
            id: $(this).attr("data-id"),
            saldo: saldo,
            nuevo_saldo: nuevo_saldo,
        });
    });

    return data;
}
