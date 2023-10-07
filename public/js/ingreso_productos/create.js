let vacio = `<tr class="vacio"><td colspan="6" class="text-center text-gray font-weight-bold">AÚN NO SE AGREGARON REGISTROS</td></tr>`;
let fila = `<tr class="fila">
            <td>#</td>
            <td><span></span><input type="hidden" name="productos[]" /></td>
            <td><span></span><input type="hidden" name="kilos[]" /></td>
            <td><span></span><input type="hidden" name="cantidades[]" /></td>
            <td class="accion"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></td>
    </tr>`;

let input_eliminado = `<input type="hidden" name="eliminados[]"/>`;

// NUEVA FUNCIONALIDAD
let ingreso_producto_id = $("#ingreso_producto_id");
let txtNomPrincipal = $("#txtNomPrincipal");
let txtTotalKilos = $("#txtTotalKilos");
let txtTotalCantidad = $("#txtTotalCantidad");
let txtSaldoKilos = $("#txtSaldoKilos");
let inputSaldoKilos = $("#inputSaldoKilos");
let txtSaldoCantidad = $("#txtSaldoCantidad");
let inputSaldoCantidad = $("#inputSaldoCantidad");

let kilos_disponible = 0;
let cantidad_disponible = 0;
// FIN NUEVO

let contenedor_filas = $("#contenedor_filas");
let btnAgregar = $("#btnAgregar");
let btnRegistrar = $("#btnRegistrar");

let producto_id = $("#producto_id");
let kilos = $("#kilos");
let cantidad = $("#cantidad");

let total_kilos = $("#total_kilos");
let total_cantidad = $("#total_cantidad");
let eliminados = $("#eliminados");

let principal = null;
let ingreso_producto = null;
$(document).ready(function () {
    $(".contenedor_productos").hide();
    btnRegistrar.hide();
    obteneInfoIngresoProducto();
    ingreso_producto_id.change(obteneInfoIngresoProducto);
    validaFilas();
    calculaTotal();
    btnAgregar.click(agregarProducto);

    kilos.on("keyup change", function () {
        let valor = $(this).val();
        if (valor <= kilos_disponible) {
        } else {
            $(this).val(kilos_disponible);
            swal.fire({
                title: "Error",
                icon: "error",
                text:
                    "Los kilos ingresados no puede ser mayor a " +
                    kilos_disponible,
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#bd2130",
            });
        }
    });

    cantidad.on("keyup change", function () {
        let valor = $(this).val();
        if (valor <= cantidad_disponible) {
        } else {
            $(this).val(cantidad_disponible);
            swal.fire({
                title: "Error",
                icon: "error",
                text: "La cantidad no puede ser mayor a " + cantidad_disponible,
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#bd2130",
            });
        }
    });

    contenedor_filas.on("click", "tr td.accion button", eliminaFila);
});

function obteneInfoIngresoProducto() {
    if (ingreso_producto_id.val() != "") {
        vaciarFilas();
        $.ajax({
            type: "GET",
            url: $("#urlInfoIngresoProducto").val(),
            data: {
                ingreso_producto_id: ingreso_producto_id.val(),
            },
            dataType: "json",
            success: function (response) {
                producto_id.html(response.options);
                principal = response.principal;
                ingreso_producto = response.ingreso_producto;
                // asignando valores
                txtNomPrincipal.text(principal.nombre);
                txtTotalKilos.text(ingreso_producto.saldo_kilos);
                txtTotalCantidad.text(ingreso_producto.saldo_cantidad);
                txtSaldoKilos.text(ingreso_producto.saldo_kilos);
                inputSaldoKilos.val(ingreso_producto.saldo_kilos);
                txtSaldoCantidad.text(ingreso_producto.saldo_cantidad);
                inputSaldoCantidad.val(ingreso_producto.saldo_cantidad);

                kilos_disponible = parseFloat(ingreso_producto.saldo_kilos);
                cantidad_disponible = parseFloat(
                    ingreso_producto.saldo_cantidad
                );
                if (response.html != "") {
                    contenedor_filas.html(response.html);
                    enumeraFilas();
                    calculaTotal();
                }
                if (
                    ingreso_producto.existe_ventas ||
                    ingreso_producto.existe_pagos
                ) {
                    $(document).find(".contenedor_productos").eq(0).hide();
                    btnRegistrar.hide();
                } else {
                    $(document).find(".contenedor_productos").eq(0).show();
                    btnRegistrar.show();
                }
            },
        });
        $(".contenedor_productos").show();
        btnRegistrar.show();
    } else {
        $(".contenedor_productos").hide();
        btnRegistrar.hide();
        producto_id.html("");
        principal = null;
        txtNomPrincipal.text("- Seleccione un lote -");
        kilos_disponible = 0;
        cantidad_disponible = 0;
        // asignando valores
        txtNomPrincipal.text("-");
        txtTotalKilos.text("0");
        txtTotalCantidad.text("0");
        txtSaldoKilos.text("0");
        inputSaldoKilos.val("0");
        txtSaldoCantidad.text("0");
        inputSaldoCantidad.val("0");
        vaciarFilas();
    }
}

function validaFilas() {
    let filas = contenedor_filas.children(".fila");
    if (filas.length > 0) {
        enumeraFilas();
        btnRegistrar.removeAttr("disabled");
        return true;
    } else {
        btnRegistrar.prop("disabled", true);
        contenedor_filas.html(vacio);
        return false;
    }
}

function quitaVacio() {
    let vacio = contenedor_filas.children(".vacio");
    if (vacio.length > 0) {
        vacio.remove();
    }
}

function enumeraFilas() {
    let filas = contenedor_filas.children(".fila");
    let numero_fila = 1;
    filas.each(function () {
        $(this).children("td").eq(0).text(numero_fila++);
    });
}

function calculaTotal() {
    if (validaFilas()) {
        let filas = contenedor_filas.children(".fila");
        let s_total_kilos = 0;
        let s_total_cantidad = 0;
        filas.each(function () {
            // SUMA DE KILOS Y CANTIDAD
            let kilos = parseFloat(
                $(this).children("td").eq(2).children("span").text()
            );
            let cantidad = parseFloat(
                $(this).children("td").eq(3).children("span").text()
            );
            s_total_kilos += kilos;
            s_total_cantidad += cantidad;
        });
        total_kilos.children("span").text(s_total_kilos);
        total_kilos.children("input").val(s_total_kilos);
        total_cantidad.children("span").text(s_total_cantidad);
        total_cantidad.children("input").val(s_total_cantidad);

        // actualizando diposnible principal
        kilos_disponible =
            parseFloat(ingreso_producto.total_kilos) -
            parseFloat(s_total_kilos);
        cantidad_disponible =
            parseFloat(ingreso_producto.total_cantidad) -
            parseFloat(s_total_cantidad);

        txtSaldoKilos.text(kilos_disponible);
        inputSaldoKilos.val(kilos_disponible);
        txtSaldoCantidad.text(cantidad_disponible);
        inputSaldoCantidad.val(cantidad_disponible);
    } else {
        total_kilos.children("span").text("0");
        total_kilos.children("input").val("0");
        total_cantidad.children("span").text("0");
        total_cantidad.children("input").val("0");
        // actualizando diposnible principal
        kilos_disponible = parseFloat(
            ingreso_producto
                ? ingreso_producto.producto_principal.total_kilos
                : "0"
        );
        cantidad_disponible = parseFloat(
            ingreso_producto
                ? ingreso_producto.producto_principal.total_cantidad
                : "0"
        );
        txtSaldoKilos.text(kilos_disponible);
        inputSaldoKilos.val(kilos_disponible);
        txtSaldoCantidad.text(cantidad_disponible);
        inputSaldoCantidad.val(cantidad_disponible);
    }
}

function agregarProducto() {
    if (producto_id.val() != "" && kilos.val() != "" && cantidad.val() != "") {
        quitaVacio();
        let nueva_fila = $(fila).clone();
        // AGREGANDO TEXTOS
        nueva_fila
            .children("td")
            .eq(1)
            .children("span")
            .text(producto_id.children("option:selected").text());
        nueva_fila.children("td").eq(2).children("span").text(kilos.val());
        nueva_fila.children("td").eq(3).children("span").text(cantidad.val());
        // AGREGANDO VALORES ALOS INPUTS
        nueva_fila
            .children("td")
            .eq(1)
            .children("input")
            .val(producto_id.val());
        nueva_fila.children("td").eq(2).children("input").val(kilos.val());
        nueva_fila.children("td").eq(3).children("input").val(cantidad.val());

        // AGREGAR NUEVA FILA
        contenedor_filas.append(nueva_fila);
        validaFilas();
        calculaTotal();
        enumeraFilas();
        limpiaCampos();
    } else {
        swal.fire({
            title: "Error",
            icon: "error",
            text: "Debes completar todos los campos para agregar un producto al lote",
            confirmButtonText: "Aceptar",
            confirmButtonColor: "#bd2130",
        });
    }
}

function limpiaCampos() {
    // producto_id.val('').trigger('change');
    kilos.val("");
    cantidad.val("");
}

function eliminaFila() {
    let fila = $(this).parents(".fila");
    if (fila.hasClass("existe")) {
        nuevo_eliminado = $(input_eliminado).clone();
        nuevo_eliminado.val(fila.attr("data-id"));
        eliminados.append(nuevo_eliminado);
    }
    fila.remove();
    calculaTotal();
    enumeraFilas();
    validaFilas();
}

function formatearNumero(numero) {
    // Convierte el número a una cadena
    var numeroString = numero.toString();

    // Divide la cadena en parte entera y decimal
    var partes = numeroString.split(".");

    // Formatea la parte entera con comas para separar los miles
    partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    // Combina las partes formateadas nuevamente en una sola cadena
    var numeroFormateado = partes.join(".");

    return numeroFormateado;
}

function vaciarFilas() {
    let filas = contenedor_filas.children(".fila");
    filas.each(function () {
        $(this).remove();
    });
    validaFilas();
}
