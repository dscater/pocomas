let urlListaIngresoProductos = $("#urlListaIngresoProductos");
let contenedor_listado = $("#contenedor_listado");
$(document).ready(function () {
    getIngresoProductos();
    contenedor_listado.on("click", ".pagination a.page-link", function (e) {
        e.preventDefault();
        let url = $(this).attr("href");
        let page = url.split("=")[1];
        getIngresoProductos(page);
    });
});

function getIngresoProductos(page = 1) {
    $.ajax({
        type: "GET",
        url: urlListaIngresoProductos.val(),
        data: {
            page: page,
        },
        dataType: "json",
        success: function (response) {
            contenedor_listado.html(response.html);
        },
    });
}
