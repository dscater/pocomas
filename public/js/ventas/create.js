let fila = `<tr class="fila">
                <td data-col="Nro.:">0</td>
                <td data-col="Producto:">
                    <input type="hidden" name="lote_id[]" value="0" />
                    <input type="hidden" name="productos[]" value="0" />
                    <span class="valor"></span>
                </td>
                <td data-col="C/U:">
                    <input type="hidden" name="costos[]" value="0" />
                    <span class="valor"></span>
                </td>
                <td data-col="Cantidad Kilos:">
                    <input type="hidden" name="cantidad_kilos[]" value="0" />
                    <span class="valor"></span>
                </td>
                <td data-col="Cantidad Cerdos:">
                    <input type="hidden" name="cantidads[]" value="0" />
                    <span class="valor"></span>
                </td>
                <td class="txt_total_sd text-center" data-col="Total S/D:">
                    <span class="valor"></span>
                </td>
                <td clasS="descuento" data-col="Descuento:">
                    <input type="number" step="0.01" name="descuentos[]" value="0" class="form-control"/>
                </td>
                <td data-col="Total:">
                    <input type="hidden" name="totales[]" value="0" />
                    <span class="valor"></span>
                </td>
                <td class="quitar text-center" data-col="Quitar:">
                <span class="eliminar"><i class="fa fa-times"></i></span>
                </td>
            </tr>`;
let vacio = `<tr class="vacio">
            <td colspan="8" class="text-center">Aún no se agregaron productos</td>
            </tr>`;

let formNuevoCliente = $("#formNuevoCliente");
let contenedorCarrito = $("#contenedorCarrito");
let totales = contenedorCarrito.siblings("tfoot").children("tr.totales");
let btnBuscaCliente = $("#btnBuscaCliente");
let cliente_id = $("#cliente_id");
let input_cliente = $("#input_cliente");
let select_ingreso_producto = $("#select_ingreso_producto");
let select_producto = $("#select_producto");
let input_cantidad_kilos = $("#input_cantidad_kilos");
let input_cantidad = $("#input_cantidad");

let btnRegistraCliente = $("#btnRegistraCliente");
let urlPrecioVenta = $("#urlPrecioVenta").val();

let input_nit = $("#input_nit");

let btnRegistrarVenta = $("#btnRegistrarVenta");
let btnAgregarProducto = $("#btnAgregarProducto");

let formulario = $("#formulario");

let tipo_venta = $("#tipo_venta");
let anticipo = $("#anticipo");
let saldo_anticipo = $("#saldo_anticipo");
let concepto_id = $("#concepto_id");

let monto_recibido = $("#monto_recibido");
let monto_cambio = $("#monto_cambio");

$(document).ready(function () {
    ennumeraFilas();

    monto_recibido.on("keyup change", calculaCambio);

    btnRegistraCliente.click(function () {
        registrarCliente();
    });

    select_ingreso_producto.change(function () {
        if (select_ingreso_producto.val() != "") {
            $.ajax({
                type: "GET",
                url: $("#urlProductosLote").val(),
                data: { id: select_ingreso_producto.val() },
                dataType: "json",
                success: function (response) {
                    select_producto.html(response);
                },
            });
        } else {
            select_producto.html("");
        }
    });

    tipo_venta.change(function () {
        monto_cambio.parents("tr").removeClass("oculto");

        if (
            tipo_venta.val() == "BANCO" ||
            tipo_venta.val() == "POR COBRAR" ||
            tipo_venta.val() == "ANTICIPOS"
        ) {
            monto_cambio.parents("tr").addClass("oculto");
            monto_recibido.parents("tr").addClass("oculto");
            monto_cambio.removeAttr("required");
            monto_recibido.removeAttr("required");
        } else {
            monto_cambio.prop("required", true);
            monto_recibido.prop("required", true);
        }

        if (tipo_venta.val() == "ANTICIPOS") {
            anticipo.parents(".contenedor_anticipo").removeClass("oculto");
            anticipo.prop("required", true);
            saldo_anticipo
                .parents(".contenedor_anticipo")
                .removeClass("oculto");
            saldo_anticipo.prop("required", true);
        } else {
            saldo_anticipo.parents(".contenedor_anticipo").addClass("oculto");
            anticipo.removeAttr("required");
            anticipo.parents(".contenedor_anticipo").addClass("oculto");
        }
    });

    anticipo.keyup(function () {
        if (
            parseFloat(anticipo.val()) >=
            parseFloat($("#input_monto_total").val())
        ) {
            btnRegistrarVenta.prop("disabled", true);
            swal.fire({
                title: "Error",
                icon: "error",
                text: `El anticipo no puede ser mayor o igual al monto total`,
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#bd2130",
            });
            anticipo.val("");
            saldo_anticipo.val("");
        } else {
            let total = parseFloat($("#input_monto_total").val());
            let valor_anticipo = parseFloat(anticipo.val());
            saldo_anticipo.val(total - valor_anticipo);
            ennumeraFilas();
        }
    });

    input_nit.keypress(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            getInfoCliente();
        }
    });

    btnBuscaCliente.click(function () {
        getInfoCliente();
    });

    cliente_id.change(function () {
        getInfoCliente("select");
    });

    btnAgregarProducto.click(function () {
        btnAgregarProducto.prop("disabled", true);
        if (
            select_producto.val() != "" &&
            input_cantidad_kilos.val() != "" &&
            input_cantidad_kilos.val() > 0 &&
            input_cantidad.val() != "" &&
            input_cantidad.val() > 0
        ) {
            $.ajax({
                type: "GET",
                url: $("#urlInfoVenta").val(),
                data: {
                    detalle_ingreso_id: select_producto.val(),
                    cantidad_kilos: input_cantidad_kilos.val(),
                    cantidad: input_cantidad.val(),
                },
                dataType: "json",
                success: function (response) {
                    if (response.sw) {
                        // CREAR FILAS DEACUERDO AL ARRAY OBTENIDO
                        let precio = response.precio;
                        let producto = response.producto;
                        let detalle_ingreso = response.detalle_ingreso;
                        let nueva_fila = $(fila).clone();

                        // producto
                        nueva_fila
                            .children("td")
                            .eq(1)
                            .children("span")
                            .text(producto.nombre);

                        nueva_fila
                            .children("td")
                            .eq(1)
                            .children("input")
                            .eq(0)
                            .val(detalle_ingreso.id);
                        nueva_fila
                            .children("td")
                            .eq(1)
                            .children("input")
                            .eq(1)
                            .val(producto.id);

                        // precio
                        nueva_fila
                            .children("td")
                            .eq(2)
                            .children("span")
                            .text(producto.precio);
                        nueva_fila
                            .children("td")
                            .eq(2)
                            .children("input")
                            .val(producto.precio);

                        // cantidad kilos
                        nueva_fila
                            .children("td")
                            .eq(3)
                            .children("span")
                            .text(input_cantidad_kilos.val());
                        nueva_fila
                            .children("td")
                            .eq(3)
                            .children("input")
                            .eq(0)
                            .val(input_cantidad_kilos.val());

                        // cantidad cerdos
                        nueva_fila
                            .children("td")
                            .eq(4)
                            .children("span")
                            .text(input_cantidad.val());
                        nueva_fila
                            .children("td")
                            .eq(4)
                            .children("input")
                            .eq(0)
                            .val(input_cantidad.val());

                        // obtener el total S/D
                        let precio_total = (
                            parseFloat(precio) *
                            parseFloat(input_cantidad_kilos.val())
                        ).toFixed(2);
                        nueva_fila
                            .children("td")
                            .eq(5)
                            .children("span")
                            .text(precio_total);
                        nueva_fila
                            .children("td")
                            .eq(7)
                            .children("span")
                            .text(precio_total);
                        nueva_fila
                            .children("td")
                            .eq(7)
                            .children("input")
                            .val(precio_total);
                        contenedorCarrito.append(nueva_fila);

                        ennumeraFilas();

                        input_cantidad_kilos.val("");
                        input_cantidad.val("");
                        select_producto.val("");
                        select_producto.trigger("change");
                        anticipo.val("");
                        saldo_anticipo.val("");
                    } else {
                        swal.fire({
                            title: "Error",
                            icon: "error",
                            html: `${response.msg}`,
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: "#bd2130",
                        });
                    }
                    setTimeout(function () {
                        btnAgregarProducto.removeAttr("disabled");
                    }, 300);
                },
            });
        }
        setTimeout(function () {
            btnAgregarProducto.removeAttr("disabled");
        }, 300);
    });

    // CALCULA DESCUENTO
    contenedorCarrito.on(
        "keyup change",
        ".fila td.descuento input",
        function (e) {
            e.preventDefault();
            let fila = $(this).closest("tr.fila");
            let valor = $(this).val();
            let total_sd = fila.children("td").eq(5).children("span").text();
            if (valor != "") {
                let total_cd = parseFloat(total_sd) - parseFloat(valor);
                fila.children("td")
                    .eq(7)
                    .children("span")
                    .text(total_cd.toFixed(2));
                fila.children("td")
                    .eq(7)
                    .children("input")
                    .val(total_cd.toFixed(2));
            } else {
                fila.children("td").eq(7).children("span").text(total_sd);
                fila.children("td").eq(7).children("input").val(total_sd);
            }
            ennumeraFilas();
        }
    );

    // QUITAR DEL CARRITO
    contenedorCarrito.on("click", ".fila td.quitar span", function () {
        let fila = $(this).closest("tr.fila");
        fila.remove();
        ennumeraFilas();
    });
});

function calculaCambio() {
    let total = totales.children("td").eq(3).children("input").eq(1).val();
    if (total && total.trim() != "") {
        if (monto_recibido.val().trim() != "") {
            let recibido = parseFloat(monto_recibido.val());
            let total_calculado = parseFloat(total);
            let cambio = recibido - total_calculado;
            monto_cambio.val(cambio.toFixed(2));
        } else {
            monto_cambio.val("0.00");
        }
    } else {
        monto_cambio.val("0.00");
    }
}

function getInfoCliente(tipo = "input") {
    let busca = false;
    if (tipo == "select") {
        if (cliente_id.val() != "") {
            busca = true;
        }
    } else {
        if (input_nit.val() != "") {
            busca = true;
        }
    }
    if (busca) {
        $.ajax({
            type: "GET",
            url: $("#urlInfoCliente").val(),
            data: {
                tipo: tipo,
                cliente_id: cliente_id.val(),
                ci: input_nit.val(),
            },
            dataType: "json",
            success: function (response) {
                if (response.sw) {
                    if (tipo == "select") {
                        input_nit.val(response.cliente.ci);
                    } else {
                        cliente_id
                            .val(response.cliente.id)
                            .trigger("change.select2");
                    }
                } else {
                    $("#m_ci_cliente").val(input_nit.val());
                    $("#m_nuevo_cliente").modal("show");
                    input_cliente.val("");
                    cliente_id.val("");
                    swal.fire({
                        title: "ATENCIÓN",
                        icon: "info",
                        html: "No se encontró ningun cliente con es Nro. de CI/NIT<br>Puede registrarlo en el siguiente formulario",
                    });
                }
            },
        });
        1;
    }
}

function registrarCliente() {
    let data = formNuevoCliente.serialize();
    $.ajax({
        headers: {
            "x-csrf-token": $("#token").val(),
        },
        type: "POST",
        url: $("#urlNuevoCliente").val(),
        data: data,
        dataType: "json",
        success: function (response) {
            cliente_id.append(response.html);
            cliente_id.val(response.i).trigger("change.select2");
            $("#m_nuevo_cliente").modal("hide");
            input_nit.val(response.cliente.ci);
            formNuevoCliente.find("input").val("");
        },
    });
}

function ennumeraFilas() {
    let filas = contenedorCarrito.children("tr.fila");
    let _vacio = contenedorCarrito.children("tr.vacio");
    if (filas.length > 0) {
        btnRegistrarVenta.removeAttr("disabled");
        _vacio.remove();
        let contador = 1;
        let total_monto = 0;
        let total_cantidad_kilos = 0;
        let total_cantidad = 0;
        let cant_kilos = 0;
        let cant = 0;
        let mon = 0;
        filas.each(function () {
            $(this).children("td").eq(0).text(contador);
            cant_kilos = $(this)
                .children("td")
                .eq(3)
                .children("input")
                .eq(0)
                .val();
            cant = $(this).children("td").eq(4).children("input").eq(0).val();
            mon = $(this).children("td").eq(7).children("input").val();
            total_cantidad_kilos += parseFloat(cant_kilos);
            total_cantidad += parseFloat(cant);
            total_monto += parseFloat(mon);
            contador++;
        });

        total_cantidad_kilos = parseFloat(total_cantidad_kilos).toFixed(2);
        total_cantidad = parseFloat(total_cantidad).toFixed(2);
        total_monto = parseFloat(total_monto).toFixed(2);
        // total kilos
        totales
            .children("td")
            .eq(1)
            .children("input")
            .eq(0)
            .val(total_cantidad_kilos);
        totales
            .children("td")
            .eq(1)
            .children("input")
            .eq(1)
            .val(total_cantidad_kilos);

        // total cantidad
        totales
            .children("td")
            .eq(2)
            .children("input")
            .eq(0)
            .val(total_cantidad);
        totales
            .children("td")
            .eq(2)
            .children("input")
            .eq(1)
            .val(total_cantidad);

        // total montos
        totales.children("td").eq(4).children("input").eq(0).val(total_monto);
        totales.children("td").eq(4).children("input").eq(1).val(total_monto);
        calculaCambio();
    } else {
        totales.children("td").eq(1).children("input").eq(0).val("0");
        totales.children("td").eq(2).children("input").eq(0).val("0");
        totales.children("td").eq(4).children("input").eq(0).val("0.00");
        btnRegistrarVenta.prop("disabled", true);
        if (_vacio.length == 0) {
            contenedorCarrito.html(vacio);
        }
    }
}
