-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 23-09-2023 a las 18:31:27
-- Versión del servidor: 8.0.30
-- Versión de PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pocomas_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` bigint UNSIGNED NOT NULL,
  `codigo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `estado` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`id`, `codigo`, `nombre`, `descripcion`, `fecha_registro`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'C001', 'CAJA 1', 'DESCRIPCION CAJA 1', '2021-07-20', 1, '2021-07-20 20:01:14', '2021-07-20 20:01:14'),
(2, 'C002', 'CAJA 2', 'CAJA 2', '2022-10-07', 1, '2022-10-07 17:31:16', '2022-10-07 17:31:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_centrals`
--

CREATE TABLE `caja_centrals` (
  `id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(24,2) NOT NULL,
  `concepto_id` bigint UNSIGNED NOT NULL,
  `ingreso_producto_id` bigint UNSIGNED DEFAULT '0',
  `cuenta_pagar_id` bigint UNSIGNED DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sw_egreso` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_transaccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierre_cajas`
--

CREATE TABLE `cierre_cajas` (
  `id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED NOT NULL,
  `inicio_caja_id` bigint UNSIGNED NOT NULL,
  `monto_total` decimal(24,2) NOT NULL,
  `fecha_cierre` date NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha_registro` date NOT NULL,
  `estado` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `estado` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `ci`, `razon_social`, `email`, `celular`, `fecha_registro`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'FERNANDO MACHACA', '111', NULL, '', '78945612', '2021-07-20', 1, '2021-07-20 20:03:13', '2021-07-20 20:03:13'),
(2, 'CARLOS MAMANI', '555', NULL, '', '65432111', '2021-07-20', 1, '2021-07-20 20:04:01', '2021-07-20 20:04:01'),
(3, 'FELIPE GUTIERREZ', '333', 'FEGUTIERREZ S.A.', '', '6666666', '2021-07-20', 1, '2021-07-20 20:04:36', '2023-04-06 14:43:14'),
(4, 'MARCOS PRADERA', '222', 'RAZON SOCIAL', '', '777777', '2022-10-06', 1, '2022-10-06 15:45:40', '2023-04-06 14:43:04'),
(5, 'OSCAR RAMIRES', '323', 'OSS S.A.', '', '6666666', '2023-04-06', 1, '2023-04-06 14:43:38', '2023-04-06 14:43:38'),
(6, 'MARIA MARTINEZ SAUCEDO', '343', '', '', '666666', '2023-08-18', 1, '2023-08-18 18:37:36', '2023-08-18 18:37:36'),
(7, 'MARCO SOLIZ', '777', NULL, '', '666666', '2023-09-12', 1, '2023-09-12 18:59:49', '2023-09-12 18:59:49'),
(8, 'JIMENA MAMANI', '888', NULL, '', '888888', '2023-09-12', 1, '2023-09-12 19:02:56', '2023-09-12 19:02:56'),
(9, 'RUBEN MARTINES', '999', NULL, '', '78787878', '2023-09-12', 1, '2023-09-12 19:05:26', '2023-09-12 19:05:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conceptos`
--

CREATE TABLE `conceptos` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conceptos`
--

INSERT INTO `conceptos` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'CONCEPTO 1', '2022-11-25 23:21:48', '2022-11-25 23:21:48'),
(2, 'CONCEPTO 2', '2022-11-25 23:22:08', '2022-11-25 23:22:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_clientes`
--

CREATE TABLE `cuenta_clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `total_deuda` decimal(24,2) NOT NULL,
  `cancelado` decimal(24,2) NOT NULL,
  `saldo` decimal(24,2) NOT NULL,
  `estado` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_cobrars`
--

CREATE TABLE `cuenta_cobrars` (
  `id` bigint UNSIGNED NOT NULL,
  `cuenta_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `monto_deuda` decimal(24,2) NOT NULL,
  `saldo` decimal(24,2) NOT NULL,
  `estado` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_cobrar_detalles`
--

CREATE TABLE `cuenta_cobrar_detalles` (
  `id` bigint UNSIGNED NOT NULL,
  `cuenta_cobrar_id` bigint UNSIGNED NOT NULL,
  `venta_detalle_id` bigint UNSIGNED NOT NULL,
  `monto` decimal(24,2) NOT NULL,
  `cancelado` decimal(24,2) NOT NULL,
  `valor` decimal(24,2) DEFAULT NULL,
  `saldo` decimal(24,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_pagars`
--

CREATE TABLE `cuenta_pagars` (
  `id` bigint UNSIGNED NOT NULL,
  `ingreso_producto_id` bigint UNSIGNED NOT NULL,
  `proveedor_id` bigint UNSIGNED NOT NULL,
  `monto_total` decimal(24,2) NOT NULL,
  `saldo` decimal(24,2) NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_pagar_detalles`
--

CREATE TABLE `cuenta_pagar_detalles` (
  `id` bigint UNSIGNED NOT NULL,
  `cuenta_pagar_id` bigint UNSIGNED NOT NULL,
  `monto` decimal(24,2) NOT NULL,
  `tipo_pago` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `saldo` decimal(24,2) NOT NULL,
  `total` decimal(24,2) NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_pagos`
--

CREATE TABLE `cuenta_pagos` (
  `id` bigint UNSIGNED NOT NULL,
  `cuenta_id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED DEFAULT NULL,
  `monto` decimal(24,2) NOT NULL,
  `observacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_cobro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_pago` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_usuarios`
--

CREATE TABLE `datos_usuarios` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `paterno` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `materno` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci_exp` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fono` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cel` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `datos_usuarios`
--

INSERT INTO `datos_usuarios` (`id`, `nombre`, `paterno`, `materno`, `ci`, `ci_exp`, `sexo`, `dir`, `fono`, `cel`, `email`, `fecha_ingreso`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'JUAN', 'PERES', '', '123', 'LP', 'HOMBRE', 'ZONA LOS OLIVOS CALLE 3 #333', '', '78945612', NULL, '2021-07-20', 2, '2021-07-20 20:01:30', '2021-07-20 20:01:30'),
(2, 'MARCOS', 'SOLIZ', '', '2222', 'LP', 'HOMBRE', 'LOS OLIVOS', '', '7777777', NULL, '2022-10-07', 3, '2022-10-07 21:40:46', '2022-10-07 21:40:46'),
(3, 'MARIA', 'MAMANI', '', '333', 'CB', 'MUJER', 'LOS OLIVOS', '', '7777777', NULL, '2023-09-08', 4, '2023-09-08 21:10:24', '2023-09-08 21:10:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ingresos`
--

CREATE TABLE `detalle_ingresos` (
  `id` bigint UNSIGNED NOT NULL,
  `ingreso_producto_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `kilos` double NOT NULL,
  `cantidad` double NOT NULL,
  `stock_kilos` double NOT NULL,
  `stock_cantidad` double NOT NULL,
  `precio_compra` decimal(24,2) NOT NULL,
  `anticipo` double NOT NULL,
  `anticipo_kilos` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED NOT NULL,
  `nro_factura` bigint NOT NULL,
  `cliente` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galerias`
--

CREATE TABLE `galerias` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `galerias`
--

INSERT INTO `galerias` (`id`, `nombre`, `tipo`, `descripcion`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 'GALERÍA', '2X4', '', '2021-05-31', '2021-05-31 16:39:41', '2021-06-07 15:00:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria_imagens`
--

CREATE TABLE `galeria_imagens` (
  `id` bigint UNSIGNED NOT NULL,
  `galeria_id` bigint UNSIGNED NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `galeria_imagens`
--

INSERT INTO `galeria_imagens` (`id`, `galeria_id`, `imagen`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 1, 'GALERÍA_1626811377.jpg', '2021-07-20', '2021-07-20 20:02:57', '2021-07-20 20:02:57'),
(2, 1, 'GALERÍA_1665073370.jpg', '2022-10-06', '2022-10-06 16:22:50', '2022-10-06 16:22:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_cajas`
--

CREATE TABLE `ingreso_cajas` (
  `id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED NOT NULL,
  `inicio_caja_id` bigint UNSIGNED NOT NULL,
  `tipo_movimiento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `registro_id` bigint UNSIGNED NOT NULL,
  `monto_total` decimal(24,2) NOT NULL,
  `concepto_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `sw_egreso` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `estado` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_productos`
--

CREATE TABLE `ingreso_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `nro_lote` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proveedor_id` bigint UNSIGNED NOT NULL,
  `tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_kilos` double NOT NULL,
  `total_cantidad` double NOT NULL,
  `precio_total` decimal(24,2) NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo` decimal(24,2) NOT NULL,
  `precio_compra` decimal(24,2) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_registro` date NOT NULL,
  `estado` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inicio_cajas`
--

CREATE TABLE `inicio_cajas` (
  `id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED NOT NULL,
  `monto_inicial` decimal(24,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha_registro` date NOT NULL,
  `estado` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex_productos`
--

CREATE TABLE `kardex_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `detalle_ingreso_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `detalle` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(24,2) DEFAULT NULL,
  `tipo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ingreso_c` double DEFAULT NULL,
  `salida_c` double DEFAULT NULL,
  `saldo_c` double DEFAULT NULL,
  `cu` decimal(24,2) DEFAULT NULL,
  `ingreso_m` decimal(24,2) DEFAULT NULL,
  `salida_m` decimal(24,2) DEFAULT NULL,
  `saldo_m` decimal(24,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mermas`
--

CREATE TABLE `mermas` (
  `id` bigint UNSIGNED NOT NULL,
  `detalle_ingreso_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `cantidad_kilos` double NOT NULL,
  `cantidad` double NOT NULL,
  `porcentaje` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2022_10_05_184432_create_proveedors_table', 1),
(2, '2022_10_05_184528_create_cuenta_pagars_table', 2),
(4, '2022_10_05_190136_create_movimiento_centrals_table', 4),
(5, '2022_10_06_101622_create_detalle_ingresos_table', 5),
(6, '2022_10_05_185339_create_caja_centrals_table', 6),
(7, '2022_11_25_191545_create_conceptos_table', 7),
(8, '2022_12_02_153910_create_cuenta_cobrar_detalles_table', 8),
(9, '2022_12_03_110018_create_mermas_table', 9),
(10, '2023_01_07_095703_create_cuenta_pagar_detalles_table', 10),
(11, '2023_08_18_161833_create_cuenta_clientes_table', 11),
(12, '2023_09_02_120004_create_bancos_table', 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint UNSIGNED NOT NULL,
  `codigo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(24,2) NOT NULL,
  `stock_minimo` double NOT NULL,
  `stock_actual` double NOT NULL,
  `stock_actual_cantidad` double NOT NULL,
  `estado` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prioridad` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `nombre`, `descripcion`, `precio`, `stock_minimo`, `stock_actual`, `stock_actual_cantidad`, `estado`, `foto`, `prioridad`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 'P001', 'PRODUCTO 1', '', 25.00, 10, 0, 0, 'ACTIVO', 'producto_default.png', 'NORMAL', '2022-11-26', 1, '2022-11-26 12:49:24', '2023-09-23 16:58:10'),
(2, 'P002', 'PRODUCTO 2', '', 35.00, 10, 0, 0, 'ACTIVO', 'producto_default.png', 'PRINCIPAL', '2022-11-26', 1, '2022-11-26 12:54:45', '2023-09-23 18:28:03'),
(3, 'P002', 'PRODUCTO 3', '', 100.00, 20, 0, 0, 'ACTIVO', 'producto_default.png', 'NORMAL', '2023-08-10', 1, '2023-08-11 01:06:13', '2023-09-23 16:58:41'),
(4, 'P004', 'PRODUCTO 4', 'P# 4', 35.00, 20, 0, 0, 'ACTIVO', 'producto_default.png', 'DEL PRINCIPAL', '2023-09-02', 1, '2023-09-02 15:39:12', '2023-09-23 18:14:41'),
(5, 'P005', 'PRODUCTO 5', 'DESC. PROD #5', 45.00, 5, 0, 0, 'ACTIVO', 'producto_default.png', 'NORMAL', '2023-09-23', 1, '2023-09-23 18:12:41', '2023-09-23 18:28:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedors`
--

CREATE TABLE `proveedors` (
  `id` bigint UNSIGNED NOT NULL,
  `propietario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fono` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedors`
--

INSERT INTO `proveedors` (`id`, `propietario`, `razon_social`, `fono`, `dir`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 'JUAN PEREZ', 'PEREZ S.A.', '22222', '', '2022-11-26', '2022-11-26 12:48:44', '2022-11-26 12:48:44'),
(2, 'ALFREDO CONDORI', 'DORI S.A.', '66666', '', '2023-09-08', '2023-09-08 15:13:57', '2023-09-08 15:13:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `razon_socials`
--

CREATE TABLE `razon_socials` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_propietario` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pais` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ciudad` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_aut` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fono` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cel` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actividad_economica` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `razon_socials`
--

INSERT INTO `razon_socials` (`id`, `nombre`, `alias`, `nombre_propietario`, `pais`, `ciudad`, `dir`, `nit`, `nro_aut`, `fono`, `cel`, `correo`, `logo`, `actividad_economica`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 'EMPRESA PRUEBA', 'EP', 'JUAN PEREZ', 'BOLIVIA', 'LA PAZ', 'ZONA LOS OLIVOS CALLE 3 #3232', '100000111111', '1000001555', '21134568', '78945612', '', 'logo.png', 'ACTIVIDAD ECONOMICA', '2021-05-31', '2021-05-31 16:39:41', '2021-06-07 14:49:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesion_users`
--

CREATE TABLE `sesion_users` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `navegador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dispositivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sistema` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `tipo`, `foto`, `estado`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$wuTuUildBSgVYfvHomvXWuVLF6T1PWByEZJelnC9LOSQ/Xwcw2N3S', 'ADMINISTRADOR', 'user_default.png', 'ACTIVO', 1, '2021-05-31 16:39:41', '2021-05-31 16:39:41'),
(2, 'JPERES', '$2y$10$NJiXTyBObLxexPj6WISBIOs/4q18P0xuGxeXmekAH52Liib0LY3TK', 'CAJA', 'user_default.png', 'ACTIVO', 1, '2021-07-20 20:01:30', '2021-07-20 20:01:30'),
(3, 'MSOLIZ', '$2y$10$tWqEk4T8rmLj78d/15QN6.K7oZXFURvUsp8pIfrS.cTUJMB/5mj2S', 'SUPERVISOR', 'user_default.png', 'ACTIVO', 1, '2022-10-07 21:40:46', '2022-10-07 21:40:46'),
(4, 'MMAMANI', '$2y$10$96KxDNapFr0YMPg11rLlmOrzR/hDBPtxAA72sn8VKk9zIQ9S3PIhS', 'CAJA', 'user_default.png', 'ACTIVO', 1, '2023-09-08 21:10:24', '2023-09-08 21:10:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_cajas`
--

CREATE TABLE `user_cajas` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_cajas`
--

INSERT INTO `user_cajas` (`id`, `user_id`, `caja_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2021-07-20 20:01:30', '2021-07-20 20:01:30'),
(2, 4, 2, '2023-09-08 21:10:24', '2023-09-08 21:10:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `cantidad_total_kilos` double NOT NULL,
  `cantidad_total` double NOT NULL,
  `anticipo` decimal(24,2) NOT NULL,
  `saldo` decimal(24,2) NOT NULL,
  `monto_total` decimal(24,2) NOT NULL,
  `tipo_venta` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_venta` date NOT NULL,
  `hora_venta` time NOT NULL,
  `monto_recibido` decimal(24,2) DEFAULT NULL,
  `monto_cambio` decimal(24,2) DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `estado` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `detalle_ingreso_id` bigint UNSIGNED NOT NULL,
  `cantidad_kilos` double NOT NULL,
  `cantidad` double NOT NULL,
  `monto` decimal(24,2) NOT NULL,
  `descuento` decimal(24,2) NOT NULL,
  `sub_total` decimal(24,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `caja_centrals`
--
ALTER TABLE `caja_centrals`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cierre_cajas`
--
ALTER TABLE `cierre_cajas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cierre_cajas_caja_id_foreign` (`caja_id`),
  ADD KEY `cierre_cajas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `conceptos`
--
ALTER TABLE `conceptos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuenta_clientes`
--
ALTER TABLE `cuenta_clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuenta_cobrars`
--
ALTER TABLE `cuenta_cobrars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuenta_cobrars_venta_id_foreign` (`venta_id`),
  ADD KEY `cuenta_cobrars_cliente_id_foreign` (`cliente_id`);

--
-- Indices de la tabla `cuenta_cobrar_detalles`
--
ALTER TABLE `cuenta_cobrar_detalles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuenta_pagars`
--
ALTER TABLE `cuenta_pagars`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuenta_pagar_detalles`
--
ALTER TABLE `cuenta_pagar_detalles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuenta_pagos`
--
ALTER TABLE `cuenta_pagos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `datos_usuarios`
--
ALTER TABLE `datos_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `datos_usuarios_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_ingresos_ingreso_producto_id_foreign` (`ingreso_producto_id`),
  ADD KEY `detalle_ingresos_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `facturas_venta_id_foreign` (`venta_id`);

--
-- Indices de la tabla `galerias`
--
ALTER TABLE `galerias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `galeria_imagens`
--
ALTER TABLE `galeria_imagens`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ingreso_cajas`
--
ALTER TABLE `ingreso_cajas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caja_id` (`caja_id`);

--
-- Indices de la tabla `ingreso_productos`
--
ALTER TABLE `ingreso_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inicio_cajas`
--
ALTER TABLE `inicio_cajas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inicio_cajas_caja_id_foreign` (`caja_id`),
  ADD KEY `inicio_cajas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kardex_productos_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `mermas`
--
ALTER TABLE `mermas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proveedors`
--
ALTER TABLE `proveedors`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `razon_socials`
--
ALTER TABLE `razon_socials`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sesion_users`
--
ALTER TABLE `sesion_users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user_cajas`
--
ALTER TABLE `user_cajas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_cajas_user_id_foreign` (`user_id`),
  ADD KEY `user_cajas_caja_id_foreign` (`caja_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_caja_id_foreign` (`caja_id`),
  ADD KEY `ventas_user_id_foreign` (`user_id`),
  ADD KEY `ventas_cliente_id_foreign` (`cliente_id`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `caja_centrals`
--
ALTER TABLE `caja_centrals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cierre_cajas`
--
ALTER TABLE `cierre_cajas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `conceptos`
--
ALTER TABLE `conceptos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cuenta_clientes`
--
ALTER TABLE `cuenta_clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuenta_cobrars`
--
ALTER TABLE `cuenta_cobrars`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuenta_cobrar_detalles`
--
ALTER TABLE `cuenta_cobrar_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuenta_pagars`
--
ALTER TABLE `cuenta_pagars`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuenta_pagar_detalles`
--
ALTER TABLE `cuenta_pagar_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuenta_pagos`
--
ALTER TABLE `cuenta_pagos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `datos_usuarios`
--
ALTER TABLE `datos_usuarios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `galerias`
--
ALTER TABLE `galerias`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `galeria_imagens`
--
ALTER TABLE `galeria_imagens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ingreso_cajas`
--
ALTER TABLE `ingreso_cajas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingreso_productos`
--
ALTER TABLE `ingreso_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inicio_cajas`
--
ALTER TABLE `inicio_cajas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mermas`
--
ALTER TABLE `mermas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proveedors`
--
ALTER TABLE `proveedors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `razon_socials`
--
ALTER TABLE `razon_socials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sesion_users`
--
ALTER TABLE `sesion_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `user_cajas`
--
ALTER TABLE `user_cajas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cierre_cajas`
--
ALTER TABLE `cierre_cajas`
  ADD CONSTRAINT `cierre_cajas_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `cierre_cajas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `cuenta_cobrars`
--
ALTER TABLE `cuenta_cobrars`
  ADD CONSTRAINT `cuenta_cobrars_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `cuenta_cobrars_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`);

--
-- Filtros para la tabla `datos_usuarios`
--
ALTER TABLE `datos_usuarios`
  ADD CONSTRAINT `datos_usuarios_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  ADD CONSTRAINT `detalle_ingresos_ingreso_producto_id_foreign` FOREIGN KEY (`ingreso_producto_id`) REFERENCES `ingreso_productos` (`id`),
  ADD CONSTRAINT `detalle_ingresos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`);

--
-- Filtros para la tabla `ingreso_cajas`
--
ALTER TABLE `ingreso_cajas`
  ADD CONSTRAINT `ingreso_cajas_ibfk_1` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `inicio_cajas`
--
ALTER TABLE `inicio_cajas`
  ADD CONSTRAINT `inicio_cajas_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `inicio_cajas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  ADD CONSTRAINT `kardex_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `user_cajas`
--
ALTER TABLE `user_cajas`
  ADD CONSTRAINT `user_cajas_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `user_cajas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `ventas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `venta_detalles_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
