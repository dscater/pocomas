$(document).ready(function () {
    usuarios();
    kardex();
    ventas();
    cuentas();
    detalle_inventario_producto();
});

function usuarios() {
    var tipo = $('#m_usuarios #tipo').parents('.form-group');
    var fecha_ini = $('#m_usuarios #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_usuarios #fecha_fin').parents('.form-group');

    tipo.hide();
    $('#m_usuarios select#filtro').change(function () {
        let filtro = $(this).val();
        switch (filtro) {
            case 'todos':
                tipo.hide();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'tipo':
                tipo.show();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
        }
    });
}

function kardex() {
    var producto = $('#m_kardex #producto').parents('.form-group');
    var fecha_ini = $('#m_kardex #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_kardex #fecha_fin').parents('.form-group');

    producto.hide();
    fecha_ini.hide();
    fecha_fin.hide();
    $('#m_kardex select#filtro').change(function () {
        let filtro = $(this).val();
        switch (filtro) {
            case 'todos':
                producto.hide();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'producto':
                producto.show();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'fecha':
                producto.hide();
                fecha_ini.show();
                fecha_fin.show();
                break;
        }
    });
}

function detalle_inventario_producto(){
    var producto = $('#m_detalle_inventario_producto #producto').parents('.form-group');
    var fecha_ini = $('#m_detalle_inventario_producto #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_detalle_inventario_producto #fecha_fin').parents('.form-group');

    producto.hide();
    fecha_ini.hide();
    fecha_fin.hide();
    $('#m_detalle_inventario_producto select#filtro').change(function () {
        let filtro = $(this).val();
        switch (filtro) {
            case 'todos':
                producto.hide();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'producto':
                producto.show();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'fecha':
                producto.hide();
                fecha_ini.show();
                fecha_fin.show();
                break;
        }
    });
}

function ventas() {
    var caja = $('#m_ventas #caja').parents('.form-group');
    var fecha_ini = $('#m_ventas #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_ventas #fecha_fin').parents('.form-group');

    caja.hide();
    fecha_ini.hide();
    fecha_fin.hide();
    $('#m_ventas select#filtro').change(function () {
        let filtro = $(this).val();
        switch (filtro) {
            case 'todos':
                caja.hide();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'caja':
                caja.show();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'fecha':
                caja.hide();
                fecha_ini.show();
                fecha_fin.show();
                break;
        }
    });
}

function cuentas() {
    var cliente = $('#m_cuentas #cliente').parents('.form-group');

    cliente.hide();
    $('#m_cuentas select#filtro').change(function () {
        let filtro = $(this).val();
        switch (filtro) {
            case 'todos':
                cliente.hide();
                break;
            case 'cliente':
                cliente.show();
                break;
        }
    });
}

function cargaGraficoItinerario() {
    var filtro_g = $('#cardGrafico #filtro').val();
    var fecha_ini = $('#cardGrafico #fecha_ini').val();
    var fecha_fin = $('#cardGrafico #fecha_fin').val();
    var estado = $('#cardGrafico #estado').val();

    $.ajax({
        type: "GET",
        url: $('#urlInfoGraficoItinerarios').val(),
        data: {
            filtro: filtro_g,
            fecha_ini: fecha_ini,
            fecha_fin: fecha_fin,
            estado: estado,
        },
        dataType: "json",
        success: function (response) {
            Highcharts.chart('contenedorGrafico', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Reporte Gráfico de Itinerarios '
                },
                subtitle: {
                    text: 'SUBTITULO'
                },
                xAxis: {
                    type: 'category',
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Cantidad'
                    }
                },
                legend: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: 'Cantidad: <b>{point.y:.0f}</b>'
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Cantidad',
                    data: response.data,
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: '#FFFFFF',
                        align: 'right',
                        format: '{point.y:.0f}', // one decimal
                        y: 10, // 10 pixels down from the top
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    },
                    // color:'#00a65a',
                }],
                lang: {
                    downloadCSV: 'Descargar CSV',
                    downloadJPEG: 'Descargar imagen JPEG',
                    downloadPDF: 'Descargar Documento PDF',
                    downloadPNG: 'Descargar imagen PNG',
                    downloadSVG: 'Descargar vector de imagen SVG ',
                    downloadXLS: 'Descargar XLS',
                    viewFullscreen: 'Ver pantalla completa',
                    printChart: 'Imprimir',
                    exitFullscreen: 'Salir de pantalla completa'
                }
            });
        }
    });
}
