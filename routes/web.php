<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/cache_clear', function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    return 'Cache borrado éxitosamente<br><a href="' . route("inicio") . '">Volver al inicio</a>';
});

Route::get('expired', 'LoginController@expired')->name('expired');

Route::get('/', 'HomeController@index')->name('inicio');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/exposicion', 'ExposicionController@exposicion')->name('exposicion');

Route::middleware(['auth'])->group(function () {

    // LOGIN
    Route::post("users/actualiza_sesion/inactividad", "UserController@inactividad")->name("login.inactividad");

    // USUARIOS
    Route::get('users', 'UserController@index')->name('users.index');

    Route::get('users/create', 'UserController@create')->name('users.create');

    Route::post('users/store', 'UserController@store')->name('users.store');

    Route::get('users/edit/{usuario}', 'UserController@edit')->name('users.edit');

    Route::put('users/update/{usuario}', 'UserController@update')->name('users.update');

    Route::delete('users/destroy/{user}', 'UserController@destroy')->name('users.destroy');

    // Configuración de cuenta
    Route::GET('users/configurar/cuenta/{user}', 'UserController@config')->name('users.config');

    // contraseña
    Route::PUT('users/configurar/cuenta/update/{user}', 'UserController@cuenta_update')->name('users.config_update');

    // foto de perfil
    Route::POST('users/configurar/cuenta/update/foto/{user}', 'UserController@cuenta_update_foto')->name('users.config_update_foto');

    // RAZON SOCIAL
    Route::get('razon_social/index', 'RazonSocialController@index')->name('razon_social.index');

    Route::get('razon_social/edit/{razon_social}', 'RazonSocialController@edit')->name('razon_social.edit');

    Route::put('razon_social/update/{razon_social}', 'RazonSocialController@update')->name('razon_social.update');

    // CAJAS
    Route::get('cajas', 'CajaController@index')->name('cajas.index');

    Route::get('cajas/create', 'CajaController@create')->name('cajas.create');

    Route::post('cajas/store', 'CajaController@store')->name('cajas.store');

    Route::get('cajas/edit/{caja}', 'CajaController@edit')->name('cajas.edit');

    Route::put('cajas/update/{caja}', 'CajaController@update')->name('cajas.update');

    Route::delete('cajas/destroy/{caja}', 'CajaController@destroy')->name('cajas.destroy');

    // PRODUCTOS
    Route::get('productos', 'ProductoController@index')->name('productos.index');

    Route::get('productos/create', 'ProductoController@create')->name('productos.create');

    Route::post('productos/store', 'ProductoController@store')->name('productos.store');

    Route::get('productos/edit/{producto}', 'ProductoController@edit')->name('productos.edit');

    Route::put('productos/update/{producto}', 'ProductoController@update')->name('productos.update');

    Route::delete('productos/destroy/{producto}', 'ProductoController@destroy')->name('productos.destroy');

    Route::get('productos/getInfo/getInfoVenta', 'ProductoController@getInfoVenta')->name('productos.getInfoVenta');

    Route::get('productos/getInfo/getMedida', 'ProductoController@getMedida')->name('productos.getMedida');

    // INGRESO DE PRODUCTOS
    Route::get('ingreso_productos/getProductosLote', 'IngresoProductoController@getProductosLote')->name('ingreso_productos.getProductosLote');

    Route::get('ingreso_productos/getIngreso', 'IngresoProductoController@getIngreso')->name('ingreso_productos.getIngreso');

    Route::get('ingreso_productos', 'IngresoProductoController@index')->name('ingreso_productos.index');

    Route::get('ingreso_productos/create', 'IngresoProductoController@create')->name('ingreso_productos.create');

    Route::post('ingreso_productos/store', 'IngresoProductoController@store')->name('ingreso_productos.store');

    Route::get('ingreso_productos/edit/{ingreso_producto}', 'IngresoProductoController@edit')->name('ingreso_productos.edit');

    Route::put('ingreso_productos/update/{ingreso_producto}', 'IngresoProductoController@update')->name('ingreso_productos.update');

    Route::delete('ingreso_productos/destroy/{ingreso_producto}', 'IngresoProductoController@destroy')->name('ingreso_productos.destroy');

    // CLIENTES
    Route::get('clientes', 'ClienteController@index')->name('clientes.index');

    Route::get('clientes/create', 'ClienteController@create')->name('clientes.create');

    Route::post('clientes/store', 'ClienteController@store')->name('clientes.store');

    Route::get('clientes/edit/{cliente}', 'ClienteController@edit')->name('clientes.edit');

    Route::put('clientes/update/{cliente}', 'ClienteController@update')->name('clientes.update');

    Route::delete('clientes/destroy/{cliente}', 'ClienteController@destroy')->name('clientes.destroy');

    Route::post('clientes/addCliente/nuevo_cliente', 'ClienteController@nuevo_cliente')->name('clientes.nuevo_cliente');

    Route::get('clientes/getInfo/getInfoCliente', 'ClienteController@getInfoCliente')->name('clientes.getInfoCliente');

    Route::get('clientes/getInfo/cuentas_cobrar', 'ClienteController@cuentas_cobrar')->name('clientes.cuentas_cobrar');

    // INGRESOS EGRESOS CAJAS
    Route::get("cajas/ingreso_cajas/{caja}", "IngresoCajaController@index")->name("ingreso_cajas.index");
    Route::get("cajas/ingreso_cajas/create/{caja}", "IngresoCajaController@create")->name("ingreso_cajas.create");
    Route::post("cajas/ingreso_cajas/store", "IngresoCajaController@store")->name("ingreso_cajas.store");
    Route::get("cajas/ingreso_cajas/edit/{ingreso_caja}", "IngresoCajaController@edit")->name("ingreso_cajas.edit");
    Route::get("cajas/ingreso_cajas/show/{ingreso_caja}", "IngresoCajaController@show")->name("ingreso_cajas.show");
    Route::put("cajas/ingreso_cajas/update/{ingreso_caja}", "IngresoCajaController@update")->name("ingreso_cajas.update");
    Route::delete("cajas/ingreso_cajas/destroy/{ingreso_caja}", "IngresoCajaController@destroy")->name("ingreso_cajas.destroy");

    // INICIO DE CAJAS
    Route::get('inicio_cajas', 'InicioCajaController@index')->name('inicio_cajas.index');

    Route::get('inicio_cajas/create', 'InicioCajaController@create')->name('inicio_cajas.create');

    Route::post('inicio_cajas/store', 'InicioCajaController@store')->name('inicio_cajas.store');

    Route::get('inicio_cajas/edit/{inicio_caja}', 'InicioCajaController@edit')->name('inicio_cajas.edit');

    Route::put('inicio_cajas/update/{inicio_caja}', 'InicioCajaController@update')->name('inicio_cajas.update');

    Route::delete('inicio_cajas/destroy/{inicio_caja}', 'InicioCajaController@destroy')->name('inicio_cajas.destroy');

    // CIERRE DE CAJAS
    Route::get('cierre_cajas/pdf/{cierre_caja}', 'CierreCajaController@pdf')->name('cierre_cajas.pdf');
    Route::get('cierre_cajas', 'CierreCajaController@index')->name('cierre_cajas.index');

    Route::get('cierre_cajas/create', 'CierreCajaController@create')->name('cierre_cajas.create');

    Route::post('cierre_cajas/store', 'CierreCajaController@store')->name('cierre_cajas.store');

    Route::get('cierre_cajas/edit/{cierre_caja}', 'CierreCajaController@edit')->name('cierre_cajas.edit');

    Route::put('cierre_cajas/update/{cierre_caja}', 'CierreCajaController@update')->name('cierre_cajas.update');

    Route::delete('cierre_cajas/destroy/{cierre_caja}', 'CierreCajaController@destroy')->name('cierre_cajas.destroy');

    Route::get('cierre_cajas/ultimo_monto/getUltimoMontoCaja', 'CierreCajaController@getUltimoMontoCaja')->name('cierre_cajas.getUltimoMontoCaja');

    // CAJA CENTRAL
    Route::resource("caja_centrals", "CajaCentralController");

    // VENTAS
    Route::post('ventas/confirmar_venta/{venta}', 'VentaController@confirmar_venta')->name('ventas.confirmar_venta');

    Route::get('ventas', 'VentaController@index')->name('ventas.index');

    Route::get('ventas/anticipos', 'VentaController@anticipos')->name('ventas.anticipos');

    Route::get('ventas/create', 'VentaController@create')->name('ventas.create');

    Route::post('ventas/store', 'VentaController@store')->name('ventas.store');

    Route::get('ventas/show/{venta}', 'VentaController@show')->name('ventas.show');

    Route::get('ventas/edit/{venta}', 'VentaController@edit')->name('ventas.edit');

    Route::put('ventas/update/{venta}', 'VentaController@update')->name('ventas.update');

    Route::delete('ventas/destroy/{venta}', 'VentaController@destroy')->name('ventas.destroy');

    Route::get('orden_venta/{venta}', 'VentaController@orden_venta')->name('ventas.orden_venta');

    // PROVEEDORES
    Route::resource("proveedors", "ProveedorController");

    // CUENTAS POR COBRAR
    Route::get('cuenta_cobrars', 'CuentaCobrarController@index')->name('cuenta_cobrars.index');

    Route::get('cuenta_cobrars/create', 'CuentaCobrarController@create')->name('cuenta_cobrars.create');

    Route::post('cuenta_cobrars/store', 'CuentaCobrarController@store')->name('cuenta_cobrars.store');

    Route::get('cuenta_cobrars/edit/{cuenta_cobrar}', 'CuentaCobrarController@edit')->name('cuenta_cobrars.edit');

    Route::get('cuenta_cobrars/pagos/{cuenta_cliente}', 'CuentaCobrarController@pagos')->name('cuenta_cobrars.pagos');

    Route::put('cuenta_cobrars/update/{cuenta_cobrar}', 'CuentaCobrarController@update')->name('cuenta_cobrars.update');

    Route::delete('cuenta_cobrars/destroy/{cuenta_cobrar}', 'CuentaCobrarController@destroy')->name('cuenta_cobrars.destroy');

    Route::get('cuenta_cobrars/getInfo/getDetalleOrden', 'CuentaCobrarController@getDetalleOrden')->name('cuenta_cobrars.getDetalleOrden');

    Route::post('cuenta_cobrars/registrarPago', 'CuentaCobrarController@registrarPago')->name('cuenta_cobrars.registrarPago');

    Route::get('cuenta_cobrars/comprobante/{cuenta_cliente}', 'CuentaCobrarController@comprobante')->name('cuenta_cobrars.comprobante');

    // CUENTAS POR PAGAR
    Route::resource("cuenta_pagars", "CuentaPagarController");

    // GALERIAS / EXPOSICION DE PRODUCTOS
    Route::get('galerias', 'GaleriaController@index')->name('galerias.index');

    Route::get('galerias/create', 'GaleriaController@create')->name('galerias.create');

    Route::post('galerias/store', 'GaleriaController@store')->name('galerias.store');

    Route::get('galerias/edit/{galeria}', 'GaleriaController@edit')->name('galerias.edit');

    Route::put('galerias/update/{galeria}', 'GaleriaController@update')->name('galerias.update');

    Route::delete('galerias/destroy/{galeria_imagen}', 'GaleriaController@destroy')->name('galerias.destroy');

    Route::get('galerias/getimgs/getImgs', 'GaleriaController@getImgs')->name('galerias.getImgs');

    // CONCEPTOS
    Route::resource("conceptos", "ConceptoController");

    // MERMAS
    Route::resource("mermas", "MermaController");

    // REPORTES
    Route::get('reportes', 'ReporteController@index')->name('reportes.index');

    Route::get('reportes/usuarios', 'ReporteController@usuarios')->name('reportes.usuarios');

    Route::get('reportes/inventario', 'ReporteController@inventario')->name('reportes.inventario');

    Route::get('reportes/kardex', 'ReporteController@kardex')->name('reportes.kardex');

    Route::get('reportes/ventas', 'ReporteController@ventas')->name('reportes.ventas');

    Route::get('reportes/cuentas', 'ReporteController@cuentas')->name('reportes.cuentas');

    Route::get('reportes/grafico/g_ventas', 'ReporteController@g_ventas')->name('reportes.g_ventas');

    Route::get('reportes/grafico/info_ventas', 'ReporteController@info_ventas')->name('reportes.info_ventas');

    Route::get('reportes/ventas_diarias_producto', 'ReporteController@ventas_diarias_producto')->name('reportes.ventas_diarias_producto');
    Route::get('reportes/ventas_semanales_producto', 'ReporteController@ventas_semanales_producto')->name('reportes.ventas_semanales_producto');
    Route::get('reportes/ventas_mensuales_producto', 'ReporteController@ventas_mensuales_producto')->name('reportes.ventas_mensuales_producto');

    Route::get('reportes/ventas_diarias_cajas', 'ReporteController@ventas_diarias_cajas')->name('reportes.ventas_diarias_cajas');
    Route::get('reportes/egreso_caja', 'ReporteController@egreso_caja')->name('reportes.egreso_caja');
    Route::get('reportes/egresos_caja', 'ReporteController@egresos_caja')->name('reportes.egresos_caja');
    Route::get('reportes/consumo_diario_clientes', 'ReporteController@consumo_diario_clientes')->name('reportes.consumo_diario_clientes');
    Route::get('reportes/consumo_semanal_clientes', 'ReporteController@consumo_semanal_clientes')->name('reportes.consumo_semanal_clientes');
    Route::get('reportes/consumo_mensual_clientes', 'ReporteController@consumo_mensual_clientes')->name('reportes.consumo_mensual_clientes');
    Route::get('reportes/ventas_diarias_credito', 'ReporteController@ventas_diarias_credito')->name('reportes.ventas_diarias_credito');
    Route::get('reportes/ventas_semanales_credito', 'ReporteController@ventas_semanales_credito')->name('reportes.ventas_semanales_credito');
    Route::get('reportes/ventas_mensuales_credito', 'ReporteController@ventas_mensuales_credito')->name('reportes.ventas_mensuales_credito');
    Route::get('reportes/cuentas_cobrar_fecha', 'ReporteController@cuentas_cobrar_fecha')->name('reportes.cuentas_cobrar_fecha');
    Route::get('reportes/cuentas_cobrar_rango_fecha', 'ReporteController@cuentas_cobrar_rango_fecha')->name('reportes.cuentas_cobrar_rango_fecha');
    Route::get('reportes/estado_cuenta_cliente', 'ReporteController@estado_cuenta_cliente')->name('reportes.estado_cuenta_cliente');
    Route::get('reportes/detalle_inventario_producto', 'ReporteController@detalle_inventario_producto')->name('reportes.detalle_inventario_producto');
    Route::get('reportes/cuenta_pagar', 'ReporteController@cuenta_pagar')->name('reportes.cuenta_pagar');
    Route::get('reportes/saldo_producto', 'ReporteController@saldo_producto')->name('reportes.saldo_producto');
    Route::get('reportes/resultado_ventas', 'ReporteController@resultado_ventas')->name('reportes.resultado_ventas');
    Route::get('reportes/mermas', 'ReporteController@mermas')->name('reportes.mermas');
    Route::get('reportes/descuento_ventas', 'ReporteController@descuento_ventas')->name('reportes.descuento_ventas');
});
