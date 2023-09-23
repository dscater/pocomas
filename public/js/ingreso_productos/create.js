let vacio = `<tr class="vacio"><td colspan="6" class="text-center text-gray font-weight-bold">AÚN NO SE AGREGARON REGISTROS</td></tr>`;
let fila = `<tr class="fila">
            <td>#</td>
            <td><span></span><input type="hidden" name="productos[]" /></td>
            <td><span></span><input type="hidden" name="kilos[]" /></td>
            <td><span></span><input type="hidden" name="cantidades[]" /></td>
            <td><span></span><input type="hidden" name="precios[]" /></td>
            <td><span></span><input type="hidden" name="control_stock[]" /></td>
            <td class="accion"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></td>
    </tr>`;

let input_eliminado = `<input type="hidden" name="eliminados[]"/>`;

let contenedor_filas = $("#contenedor_filas");
let btnAgregar = $("#btnAgregar");
let btnRegistrar = $("#btnRegistrar");

let producto_id = $('#producto_id');
let kilos = $('#kilos');
let cantidad = $('#cantidad');
let precio_compra = $('#precio_compra');

let precio_total = $("#precio_total");
let total_kilos = $("#total_kilos");
let total_cantidad = $("#total_cantidad");
let eliminados = $("#eliminados");
let tipo_control = $("#tipo_control");

$(document).ready(function () {
    validaFilas();
    calculaTotal();
    btnAgregar.click(agregarProducto);

    contenedor_filas.on("click", "tr td.accion button", eliminaFila);
});

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
        $(this).children('td').eq(0).text(numero_fila++);
    });
}

function calculaTotal() {
    if (validaFilas()) {
        let filas = contenedor_filas.children(".fila");
        let total = 0;
        let s_total_kilos = 0;
        let s_total_cantidad = 0;
        filas.each(function () {
            // subtotal
            let kilos = parseFloat($(this).children("td").eq(2).children("span").text());
            let cantidad = parseFloat($(this).children("td").eq(3).children("span").text());
            let subtotal = kilos * parseFloat(precio_compra.val());
            parseFloat($(this).children("td").eq(4).children("span").text(subtotal));
            parseFloat($(this).children("td").eq(4).children("input").val(subtotal));
            total += subtotal;
            s_total_kilos += kilos;
            s_total_cantidad += cantidad;
        });
        total_kilos.children('span').text(s_total_kilos);
        total_kilos.children('input').val(s_total_kilos);
        total_cantidad.children('span').text(s_total_cantidad);
        total_cantidad.children('input').val(s_total_cantidad);
        precio_total.children('span').text(total.toFixed(2));
        precio_total.children('input').val(total);
    }
}


function agregarProducto() {
    if (producto_id.val() != "" && kilos.val() != "" && cantidad.val() != "" && precio_compra.val() != "") {
        quitaVacio();
        let nueva_fila = $(fila).clone();
        // AGREGANDO TEXTOS
        nueva_fila.children('td').eq(1).children("span").text(producto_id.children("option:selected").text());
        nueva_fila.children('td').eq(2).children("span").text(kilos.val());
        nueva_fila.children('td').eq(3).children("span").text(cantidad.val());
        nueva_fila.children('td').eq(4).children("span").text(parseFloat(precio_compra.val()).toFixed(2));
        nueva_fila.children('td').eq(5).children("span").text(tipo_control.val());
        // AGREGANDO VALORES ALOS INPUTS
        nueva_fila.children('td').eq(1).children("input").val(producto_id.val());
        nueva_fila.children('td').eq(2).children("input").val(kilos.val());
        nueva_fila.children('td').eq(3).children("input").val(cantidad.val());
        nueva_fila.children('td').eq(4).children("input").val(precio_compra.val());
        nueva_fila.children('td').eq(5).children("input").val(tipo_control.val());

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
            confirmButtonColor: "#bd2130"
        });
    }
}

function limpiaCampos() {
    // producto_id.val('').trigger('change');
    kilos.val("");
    cantidad.val("");
    // tipo_control.val("KILOS");
}

function eliminaFila() {
    let fila = $(this).parents(".fila");
    if (fila.hasClass('existe')) {
        nuevo_eliminado = $(input_eliminado).clone();
        nuevo_eliminado.val(fila.attr('data-id'));
        eliminados.append(nuevo_eliminado);
    }
    fila.remove();
    enumeraFilas();
    validaFilas();
}
