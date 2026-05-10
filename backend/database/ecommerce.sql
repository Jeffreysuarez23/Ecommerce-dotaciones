-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-06-2026 a las 20:11:04
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecommerce`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `subtitulo` varchar(250) DEFAULT NULL,
  `url_imagen` varchar(500) NOT NULL,
  `url_enlace` varchar(500) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `inicio_en` timestamp NULL DEFAULT NULL,
  `fin_en` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos`
--

CREATE TABLE `carritos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `cupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carritos`
--

INSERT INTO `carritos` (`id`, `usuario_id`, `session_id`, `cupon_id`, `creado_en`, `actualizado_en`) VALUES
(1, NULL, 'abc123', NULL, '2026-05-22 19:02:39', '2026-05-22 19:02:39'),
(2, NULL, NULL, NULL, '2026-05-26 18:12:01', '2026-05-26 18:12:01'),
(3, NULL, 'abc123', NULL, '2026-05-26 18:12:08', '2026-05-26 18:12:08'),
(4, NULL, '1781043834820', NULL, '2026-06-09 22:23:56', '2026-06-09 22:23:56'),
(5, NULL, '1781113236910', NULL, '2026-06-10 17:40:37', '2026-06-10 17:40:37'),
(6, NULL, '1781139379753', NULL, '2026-06-11 00:56:21', '2026-06-11 00:56:21'),
(7, NULL, '1781143237112', NULL, '2026-06-11 02:00:38', '2026-06-11 02:00:38'),
(8, NULL, '1781216101070', NULL, '2026-06-11 22:15:02', '2026-06-11 22:15:02'),
(9, NULL, '1781223084380', NULL, '2026-06-12 00:11:26', '2026-06-12 00:11:26'),
(10, NULL, '1781288988722', NULL, '2026-06-12 18:29:50', '2026-06-12 18:29:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_items`
--

CREATE TABLE `carrito_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `carrito_id` bigint(20) UNSIGNED NOT NULL,
  `variante_id` bigint(20) UNSIGNED NOT NULL,
  `lona_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cantidad` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito_items`
--

INSERT INTO `carrito_items` (`id`, `carrito_id`, `variante_id`, `lona_id`, `cantidad`) VALUES
(64, 5, 46, NULL, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `padre_id` bigint(20) UNSIGNED DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `destacada` tinyint(1) DEFAULT 0,
  `imagen_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `slug`, `padre_id`, `orden`, `destacada`, `imagen_url`) VALUES
(35, 'Camisas', 'camisas', NULL, 0, 1, 'http://localhost:8000/images/categorias/1781729023_6a3306ff0d461.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones_cms`
--

CREATE TABLE `configuraciones_cms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('texto','color','imagen','json','booleano') DEFAULT 'texto',
  `grupo` varchar(50) DEFAULT 'branding',
  `actualizado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pendiente','leido','resuelto') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id`, `first_name`, `last_name`, `email`, `subject`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Jeffrey', 'Suarez', 'jeffrey@gmail.com', 'order', 'no me ha llegado.', 'pendiente', '2026-06-09 02:07:59', '2026-06-09 02:07:59'),
(2, 'dsfdsffds', 'fdsdsfdsfd', 'sfdsdfsdf@gmail.com', 'product', 'que es eso?', 'pendiente', '2026-06-09 02:09:23', '2026-06-09 02:09:23'),
(3, 'dfdfsfds', 'dsffdsdfs', 'sfdsdffdsfds@gmail.com', 'return', 'quiero mi plata', 'pendiente', '2026-06-09 02:11:12', '2026-06-09 02:11:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cupones`
--

CREATE TABLE `cupones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `tipo` enum('porcentaje','fijo') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `monto_minimo_pedido` decimal(10,2) DEFAULT 0.00,
  `limite_usos` int(11) DEFAULT NULL,
  `usos_actuales` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `expira_en` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones`
--

CREATE TABLE `devoluciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `motivo` text NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada','resuelta') DEFAULT 'pendiente',
  `resolucion_admin` text DEFAULT NULL,
  `resuelto_por` bigint(20) UNSIGNED DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `nombre_recibe` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `etiqueta` varchar(50) DEFAULT 'Casa',
  `departamento` varchar(80) NOT NULL,
  `ciudad` varchar(80) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `referencia` varchar(250) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0,
  `eliminado_en` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`id`, `usuario_id`, `nombre_recibe`, `telefono`, `etiqueta`, `departamento`, `ciudad`, `direccion`, `referencia`, `codigo_postal`, `es_principal`, `eliminado_en`) VALUES
(1, 1, NULL, NULL, 'Casa', 'Atlántico', 'Barranquilla', 'Calle 123 #45-67', NULL, '080001', 1, NULL),
(2, 7, 'Prueba', '3233338603', 'Casa', 'antioquia', 'medellin', 'calle 57a #30-23', NULL, '050015', 0, NULL),
(3, 7, 'jeffrey', '3232434234', 'Casa', 'antioquia', 'medellin', 'calle 57a #30-23', NULL, '01230123', 0, NULL),
(4, 7, 'jeffrey', '234234324', 'Casa', 'antioquia', 'medellin', 'calle 57A #30-23', NULL, '342234', 0, NULL),
(5, 7, 'jeffrey', '23432432', 'Casa', 'antioquia', 'medellin', 'calle 58', NULL, '324332', 0, NULL),
(6, 7, 'prueba', '23342432', 'Casa', 'dfsdf', 'fdsdsf', 'dfsdf23', NULL, '4324', 0, NULL),
(7, 7, 'vffdg', '23423423', 'Casa', '42ffwe', 'fsdfsd', 'fewrwer', 'fd', 'f3242432', 0, NULL),
(8, 7, 'fsdf', 'dfsdfsdfs', 'Casa', 'dsffdsdsf', 'fdsfsddsf', 'fdsdsffds', NULL, '3324234', 0, NULL),
(9, 7, 'dfgfgddfg', '32442', 'Casa', 'dfsdfsdfs', 'fddsfds', 'fdssfdfd', 'dfsdfs', 'dfsfdfds', 0, NULL),
(10, 7, 'sdffsdsfd', 'fs3432432', 'Casa', 'fef3f', 'fsdffsd', '342dfdssdf', 'sdfsdf', 'w342324', 0, NULL),
(11, 7, 'sdffdsfs', 'sdffdsfds', 'Casa', 'werrwe', 'erwerw', 'werwerwerwer', 'erwrwerew', 'ewrerwrew', 0, NULL),
(12, 7, 'fdsdfsfds', 'fdsfdsfds', 'Casa', 'dfsfdsfds', 'fdsdfsfds', 'dsffsdsf', 'fdsfdsfsd', 'fdsfdfds', 0, NULL),
(13, 7, 'sfdsffds', 'sfdfdssfd', 'Casa', 'sdffdssfd', 'dfsfds', 'dsfdsf', 'dsfdsfdsdf', 'fdsfds', 0, NULL),
(14, 7, 'fsdfsdf', 'dfsdsffd', 'Casa', 'sfdfdssfd', 'fdsfdsfsd', 'fsdfsfd', 'fdsfddsf', 'fdsfdsfds', 0, NULL),
(15, 7, 'dgffgdgd', 'gfddfgdfg', 'Casa', 'dfggfddfg', 'fgddgfdfg', 'dgfgfdfg', 'fgdgfdfdg', 'fgdfdgfdg', 0, NULL),
(16, 7, 'dfsfds', 'fdsffdssdf', 'Casa', 'fdsfds', 'fsdsdfsfd', 'sdffds', 'fsdsfd', 'fsdsfd', 0, NULL),
(17, 7, 'dsfdfsfds', 'fdsfsdf', 'Casa', 'dfsfdssfd', 'fdsdfsfsdsfd', 'fdssfdsdf', 'dfssdf', 'dfsdsfsfd', 0, NULL),
(18, 7, 'ddsfs', 'dfdsfds', 'Casa', 'dfsdfsfds', 'dfsdsf', 'sdffdsdsf', 'dfsfdssd', 'fdsdsfdsf', 0, NULL),
(19, 7, 'gfddfggdf', 'fgdfgdfdg', 'Casa', 'gddf', 'dfgfdd', 'fdggfdfdg', 'fgdfgd', 'gfdgfdgf', 0, NULL),
(20, 7, 'dsffds', 'dfsdfssd', 'Casa', 'dfsdfs', 'dsfdfs', 'dfsdfs', 'dfsdfs', 'dfsdfs', 0, NULL),
(21, 7, 'werewrrew', 'ewrreewr', 'Casa', 'erwerwrew', 'erwerwwer', 'erwerw', 'rewwerrew', 'erwerw', 0, NULL),
(22, 7, 'dsfds', 'dsffsdds', 'Casa', 'sdffds', 'sfddfsfsd', 'dsfdsfsdf', 'dsfsfdsfd', 'sfdsdfsdf', 0, NULL),
(23, 7, 'dsaasd', 'asdsaads', 'Casa', 'sdasadsa', 'dsasdaas', 'sdasdadsa', 'adsdsaas', 'sdsaasd', 0, NULL),
(24, 7, 'DFDFF', 'DSFDSFDSFDS', 'Casa', 'DFSFDDSF', 'DFSDFDFS', 'DFSDFSFDS', 'DFSDFS', 'DFS', 0, NULL),
(25, 7, 'dsfdsfds', 'fdssfdfsd', 'Casa', 'dfsdfssdf', 'dfsdfsfd', 'fdssfdfsd', 'fdsdfdfsfds', 'dfsdfsfds', 0, NULL),
(26, 7, 'fdsfsfds', 'fdsfdsfdsdsfds', 'Casa', 'fdsdfs', 'sfdfdsfds', 'fdsfds', 'fdsfds', 'dfsfds', 0, NULL),
(27, 7, 'gdgf', 'gfdgfd', 'Casa', 'gdffd', 'gfdgdf', 'dfgfd', 'dgfdgfd', 'gdfgf', 0, NULL),
(28, 7, 'fdggfd', 'rr', 'Casa', 'gfdgfd', 'gfdgdfgfd', 'fgddgfdgf', 'dfdfg', 'gfdgfdg', 0, NULL),
(29, 7, 'fdsfds', 'dfsfds', 'Casa', 'fsdfds', 'fdsfds', 'fdsdfs', 'fsdfsdfds', 'fdsfds', 0, NULL),
(30, 7, 'dsfds', 'dfsfdsfds', 'Casa', 'sfdsfd', 'sfdfdssfd', 'dfsdfs', 'dfsdfs', 'sfddsf', 0, NULL),
(31, 7, 'fdsfds', 'dfsfds', 'Casa', 'fdsfdsfsd', 'dfsfdsfds', 'fsdfsdfsd', 'dfssfd', 'dsfsfd', 0, NULL),
(32, 7, 'dsfdfs', 'dfsdfs', 'Casa', 'dfsdfs', 'dfsdfsfd', 'dfsdfs', 'dfsdfs', 'sdfsfds', 0, NULL),
(33, 7, 'sdffds', 'dfsdfs', 'Casa', 'dsfdfs', 'dfssfd', 'dfsdfsfds', 'fdsdfsfsd', 'dsfdfsdfs', 0, NULL),
(34, 7, 'dfsdfs', 'fdsfds', 'Casa', 'dfsfds', 'dfsdfs', 'dfsfds', 'fdsdfs', 'dfsdfs', 0, NULL),
(35, 7, 'fdsdfs', 'fdssdfsdf', 'Casa', 'sdfsfdfds', 'fdsfds', 'fdsdfs', 'sdffdssdf', 'fdsdfs', 0, NULL),
(36, 7, 'fdsdsf', 'dfsdsf', 'Casa', 'fdsdfsdfs', 'fdsfdssfd', 'dfsdfs', 'sfddfsdfs', 'dfsfds', 0, NULL),
(37, 7, 'fdsfsd', 'sdsdfsd', 'Casa', 'dfsfds', 'fdsdfs', 'dsfdfs', 'dfssfdfsd', 'dfsdfsfds', 0, NULL),
(38, 7, 'sdfds', 'dsfdfsf', 'Casa', 'dsfds', 'fdsfsd', 'fdsfdsfds', 'dfsfdsfds', 'fdsfds', 0, NULL),
(39, 7, 'dfdfsd', 'sfdsfdfsd', 'Casa', 'dsfdfssfd', 'fdssfd', 'dfsfdsfds', 'ddfsdfsfds', 'dfsdfs', 0, NULL),
(40, 7, 'John Doe', '+573000000000', 'Casa', 'Atlantico', 'Barranquilla', 'Calle 123 #45-67', NULL, '080001', 0, NULL),
(41, 7, 'dfssdfdfs', 'dfsdfsfds', 'Casa', 'dfsfds', 'dfsdfs', 'fdssfdfds', 'dfsdsf', 'sfdfds', 0, NULL),
(42, 7, 'ewrrew', 'rewrewrew', 'Casa', 'rewerw', 'rewerw', 'rewrewwer', 'rewerw', 'erwerw', 0, NULL),
(43, 7, 'jeffrey suarez', '3233338603', 'Casa', 'Antioquia', 'Medellín', 'calle 34fds', 'fdsfdsfds', '423432', 0, NULL),
(44, 7, 'jeffrey', '3423423423', 'Casa', 'Guainía', 'Inírida', 'scsdffdsf', 'wwer', '233243', 0, NULL),
(45, 7, 'fdsfdsfds', '2343424324', 'Casa', 'Amazonas', 'Leticia', 'fdsdsfdfssdf', 'fdsdfsdfs', '343232', 0, NULL),
(46, 7, 'fdsfdsfds', '2343424324', 'Casa', 'Amazonas', 'Leticia', 'fdsdsfdfssdf', 'fdsdfsdfs', '343232', 0, NULL),
(47, 7, 'sdsdffd', '3244323422', 'Casa', 'Antioquia', 'Apartadó', '3dfsdfsfds', 'fdsfds', '324342', 0, NULL),
(48, 7, 'dfsdfsdfs', '2433242343', 'Casa', 'Guainía', 'Inírida', 'rerewwer', 'erwererwerw', '234342', 0, NULL),
(49, 7, 'fewrrewwer', '3244323421', 'Casa', 'Caquetá', 'Florencia', 'fsdfdsfds', 'dfsfdsfds', '323243', 0, NULL),
(50, 7, 'JFDSNFD', '3422342434', 'Casa', 'Atlántico', 'Malambo', 'DFSDSFFDS', 'FSDSDFDFS', '324432', 0, NULL),
(51, 7, 'hfdjsdf', '4322432433', 'Casa', 'Antioquia', 'Medellín', 'fdsfdsdfsfds', 'fdsdfsfds', '324342', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dotaciones`
--

CREATE TABLE `dotaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `min_lonas` tinyint(3) UNSIGNED DEFAULT 3,
  `max_lonas` tinyint(3) UNSIGNED DEFAULT 10,
  `lonas_activas` int(11) DEFAULT 0,
  `alerta_enviada_en` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dotaciones`
--

INSERT INTO `dotaciones` (`id`, `nombre`, `descripcion`, `min_lonas`, `max_lonas`, `lonas_activas`, `alerta_enviada_en`) VALUES
(17, 'zapatos', 'fjfjhfjf', 1, 10, 0, NULL),
(18, 'Camisas', NULL, 1, 10, 0, NULL),
(19, 'dotac alpuma', NULL, 1, 10, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios`
--

CREATE TABLE `envios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `transportadora` varchar(100) DEFAULT NULL,
  `guia` varchar(100) DEFAULT NULL,
  `estado` enum('preparando','enviado','en_ruta','entregado','fallido') DEFAULT 'preparando',
  `fecha_entrega_estimada` date DEFAULT NULL,
  `entregado_en` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_lonas`
--

CREATE TABLE `historial_lonas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lona_id` bigint(20) UNSIGNED NOT NULL,
  `orden_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accion` enum('descuento','ajuste_manual','ingreso','agotado') NOT NULL,
  `talla` varchar(10) DEFAULT NULL,
  `cantidad_cambio` int(11) DEFAULT NULL,
  `cantidad_restante` int(11) DEFAULT NULL,
  `snapshot_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Estado de tallas al momento del evento' CHECK (json_valid(`snapshot_json`)),
  `notas` text DEFAULT NULL,
  `creado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_lonas`
--

INSERT INTO `historial_lonas` (`id`, `lona_id`, `orden_item_id`, `accion`, `talla`, `cantidad_cambio`, `cantidad_restante`, `snapshot_json`, `notas`, `creado_por`, `creado_en`) VALUES
(87, 11, NULL, 'ingreso', 'XS', 5, NULL, NULL, 'Variable reubicada a esta lona/talla (SKU: CAMISASENA)', NULL, '2026-06-17 20:48:07'),
(88, 11, NULL, 'ingreso', 'S', 5, NULL, NULL, 'Creación de nueva variable de producto (SKU: CAMISASENA)', NULL, '2026-06-17 20:48:08'),
(89, 11, NULL, 'ingreso', 'M', 6, NULL, NULL, 'Creación de nueva variable de producto (SKU: CAMISASENA)', NULL, '2026-06-17 20:48:54'),
(90, 11, NULL, 'descuento', 'XS', -5, 0, NULL, 'Venta Orden #50 | Variante 45', NULL, '2026-06-17 20:50:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_producto`
--

CREATE TABLE `imagenes_producto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `variante_id` bigint(20) UNSIGNED DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `es_portada` tinyint(1) DEFAULT 0,
  `orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `imagenes_producto`
--

INSERT INTO `imagenes_producto` (`id`, `producto_id`, `variante_id`, `url`, `es_portada`, `orden`) VALUES
(21, 24, NULL, 'http://localhost:8000/images/productos/1781729214_6a3307be8929e.jpg', 1, 0),
(22, 24, NULL, 'http://localhost:8000/images/productos/1781729215_6a3307bfc2e55.jpg', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lonas`
--

CREATE TABLE `lonas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dotacion_id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `tipo_producto` varchar(80) DEFAULT NULL,
  `categoria_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('nuevo','usado') DEFAULT 'nuevo',
  `activa` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `capacidad_maxima` int(11) NOT NULL DEFAULT 50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lonas`
--

INSERT INTO `lonas` (`id`, `dotacion_id`, `codigo`, `tipo_producto`, `categoria_id`, `estado`, `activa`, `creado_en`, `capacidad_maxima`) VALUES
(10, 17, 'LONA-001', 'camisa', NULL, 'nuevo', 0, '2026-06-17 20:16:17', 500),
(11, 18, 'LONA-002', 'Camisilla', 35, 'nuevo', 1, '2026-06-17 20:45:33', 500),
(12, 18, 'GTJU', 'redondo', 35, 'nuevo', 1, '2026-06-17 21:20:16', 500);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lona_tallas`
--

CREATE TABLE `lona_tallas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lona_id` bigint(20) UNSIGNED NOT NULL,
  `talla` varchar(10) NOT NULL,
  `cantidad` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lona_tallas`
--

INSERT INTO `lona_tallas` (`id`, `lona_id`, `talla`, `cantidad`) VALUES
(29, 11, 'XS', 0),
(30, 11, 'S', 5),
(31, 11, 'M', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(5, '2026_06_02_205429_create_jobs_table', 1),
(6, '2026_06_02_205446_create_job_batches_table', 2),
(7, '2026_06_02_205513_create_cache_table', 3),
(8, '2026_06_08_210453_create_contactos_table', 4),
(9, '2026_06_10_180544_add_capacidad_maxima_to_lonas_table', 5),
(10, '2026_06_12_183639_add_pagado_estado_to_ordenes_table', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'NULL = Para todos los admins',
  `tipo` enum('stock_bajo','orden','sistema','marketing') DEFAULT 'sistema',
  `titulo` varchar(200) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `leido_en` timestamp NULL DEFAULT NULL,
  `confirmado_por` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Doble check de la BD vieja',
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `tipo`, `titulo`, `mensaje`, `leido_en`, `confirmado_por`, `creado_en`) VALUES
(1, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781138544 por $204,000 COP', '2026-06-13 05:46:16', NULL, '2026-06-11 05:42:24'),
(2, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781138972 por $198,600 COP', '2026-06-13 05:46:15', NULL, '2026-06-11 05:49:32'),
(3, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781139431 por $178,900 COP', '2026-06-13 05:46:14', NULL, '2026-06-11 05:57:11'),
(4, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781139525 por $106,800 COP', '2026-06-13 05:46:13', NULL, '2026-06-11 05:58:45'),
(5, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781139891 por $106,800 COP', '2026-06-13 05:46:12', NULL, '2026-06-11 06:04:51'),
(6, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781140455 por $576,800 COP', '2026-06-13 05:46:10', NULL, '2026-06-11 06:14:15'),
(7, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781140585 por $106,800 COP', '2026-06-13 05:46:09', NULL, '2026-06-11 06:16:25'),
(8, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781141650 por $106,800 COP', '2026-06-13 05:46:08', NULL, '2026-06-11 06:34:10'),
(9, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781142570 por $106,800 COP', '2026-06-13 05:46:07', NULL, '2026-06-11 06:49:30'),
(10, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781142679 por $106,800 COP', '2026-06-13 05:46:05', NULL, '2026-06-11 06:51:19'),
(11, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781142772 por $106,800 COP', '2026-06-13 05:46:04', NULL, '2026-06-11 06:52:52'),
(12, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781142945 por $106,800 COP', '2026-06-13 05:46:02', NULL, '2026-06-11 06:55:45'),
(13, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781143082 por $106,800 COP', '2026-06-13 05:46:01', NULL, '2026-06-11 06:58:02'),
(14, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781143272 por $106,800 COP', '2026-06-13 05:46:00', NULL, '2026-06-11 07:01:12'),
(15, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781143499 por $106,800 COP', '2026-06-13 05:45:59', NULL, '2026-06-11 07:04:59'),
(16, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781143616 por $106,800 COP', '2026-06-13 05:45:57', NULL, '2026-06-11 07:06:56'),
(17, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781143721 por $106,800 COP', '2026-06-13 05:45:56', NULL, '2026-06-11 07:08:41'),
(18, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781143825 por $106,800 COP', '2026-06-13 05:45:55', NULL, '2026-06-11 07:10:25'),
(19, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781143907 por $106,800 COP', '2026-06-13 05:45:54', NULL, '2026-06-11 07:11:47'),
(20, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781211553 por $106,800 COP', '2026-06-13 05:45:53', NULL, '2026-06-12 01:59:13'),
(21, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781212578 por $106,800 COP', '2026-06-13 05:45:51', NULL, '2026-06-12 02:16:18'),
(22, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781213495 por $106,800 COP', '2026-06-13 05:45:50', NULL, '2026-06-12 02:31:35'),
(23, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781215077 por $106,800 COP', '2026-06-13 05:45:49', NULL, '2026-06-12 02:57:57'),
(24, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781215759 por $106,800 COP', '2026-06-13 05:45:48', NULL, '2026-06-12 03:09:19'),
(25, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781215818 por $106,800 COP', '2026-06-13 05:45:46', NULL, '2026-06-12 03:10:18'),
(26, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781216123 por $106,800 COP', '2026-06-13 05:45:45', NULL, '2026-06-12 03:15:23'),
(27, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781216237 por $106,800 COP', '2026-06-13 05:45:44', NULL, '2026-06-12 03:17:17'),
(28, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781224764 por $215,000 COP', '2026-06-13 05:45:43', NULL, '2026-06-12 05:39:24'),
(29, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781225070 por $106,800 COP', '2026-06-13 05:45:42', NULL, '2026-06-12 05:44:30'),
(30, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781225186 por $106,800 COP', '2026-06-13 05:45:41', NULL, '2026-06-12 05:46:26'),
(31, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781225459 por $110,950 COP', '2026-06-13 05:45:39', NULL, '2026-06-12 05:50:59'),
(32, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781225818 por $215,000 COP', '2026-06-13 05:45:38', NULL, '2026-06-12 05:56:58'),
(33, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781289011 por $106,800 COP', '2026-06-13 05:45:36', NULL, '2026-06-12 23:30:11'),
(34, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781289580 por $106,800 COP', '2026-06-13 05:45:34', NULL, '2026-06-12 23:39:40'),
(35, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781293900 por $387,750 COP', '2026-06-13 05:45:29', NULL, '2026-06-13 00:51:41'),
(36, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781294350 por $106,800 COP', '2026-06-13 05:45:27', NULL, '2026-06-13 00:59:10'),
(37, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781294664 por $106,800 COP', '2026-06-13 05:45:26', NULL, '2026-06-13 01:04:24'),
(38, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781294830 por $106,800 COP', '2026-06-13 05:45:35', NULL, '2026-06-13 01:07:10'),
(39, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781300698 por $551,850 COP', '2026-06-13 05:45:34', NULL, '2026-06-13 02:44:58'),
(40, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781310013 por $198,600 COP', '2026-06-13 05:45:17', NULL, '2026-06-13 05:20:13'),
(41, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781311877 por $287,850 COP', '2026-06-13 05:52:12', NULL, '2026-06-13 05:51:17'),
(42, NULL, 'stock_bajo', 'Producto Agotado', 'El producto Camisa Sena (Talla: XS, Color: ROJO) se ha quedado sin stock (0 unidades).', '2026-06-13 06:00:16', NULL, '2026-06-13 05:51:17'),
(43, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781312309 por $865,200 COP', '2026-06-13 06:00:15', NULL, '2026-06-13 05:58:29'),
(44, NULL, 'stock_bajo', 'Producto Agotado', 'El producto Camisa Sena (Talla: M, Color: ROJO) se ha quedado sin stock (0 unidades).', '2026-06-13 06:00:14', NULL, '2026-06-13 05:58:29'),
(45, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781312369 por $1,400,000 COP', '2026-06-13 06:00:11', NULL, '2026-06-13 05:59:29'),
(46, NULL, 'stock_bajo', 'Producto Agotado', 'El producto prueba (Talla: Única, Color: ROJO) se ha quedado sin stock (0 unidades).', '2026-06-13 06:00:10', NULL, '2026-06-13 05:59:29'),
(47, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781633992 por $200,400 COP', '2026-06-17 22:51:35', NULL, '2026-06-16 23:19:52'),
(48, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781634265 por $9,180,000 COP', '2026-06-17 22:51:34', NULL, '2026-06-16 23:24:25'),
(49, NULL, 'stock_bajo', 'Producto Agotado', 'El producto camisa (Talla: S, Color: AZUL) se ha quedado sin stock (0 unidades).', '2026-06-17 22:51:33', NULL, '2026-06-16 23:24:25'),
(50, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781636224 por $105,900 COP', '2026-06-17 22:51:32', NULL, '2026-06-16 23:57:04'),
(51, NULL, 'stock_bajo', 'Stock Crítico', 'El producto Pantalones negros (Talla: XS, Color: NEGRO) tiene stock bajo (3 unidades).', '2026-06-17 22:51:32', NULL, '2026-06-16 23:57:04'),
(52, NULL, 'orden', 'Nueva orden recibida', 'Se ha creado la orden ORD-1781729424 por $454,500 COP', NULL, NULL, '2026-06-18 01:50:24'),
(53, NULL, 'stock_bajo', 'Producto Agotado', 'El producto Camisa sena (Talla: XS, Color: #57fa00) se ha quedado sin stock (0 unidades).', NULL, NULL, '2026-06-18 01:50:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `direccion_id` bigint(20) UNSIGNED NOT NULL,
  `cupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `numero` varchar(30) NOT NULL,
  `estado` enum('pendiente','pagado','confirmada','procesando','enviado','entregado','cancelada','devuelta') DEFAULT 'pendiente',
  `tipo_precio` enum('minorista','mayorista') DEFAULT 'minorista',
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `envio_costo` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `notas_cliente` text DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id`, `usuario_id`, `direccion_id`, `cupon_id`, `numero`, `estado`, `tipo_precio`, `subtotal`, `descuento`, `envio_costo`, `total`, `notas_cliente`, `creado_en`) VALUES
(47, 7, 48, NULL, 'ORD-1781633992', 'pagado', 'minorista', 185400.00, 0.00, 15000.00, 200400.00, 'fdsdfsfsd', '2026-06-16 18:19:52'),
(48, 7, 49, NULL, 'ORD-1781634265', 'pagado', 'minorista', 9180000.00, 0.00, 0.00, 9180000.00, 'fdsdfsdfsdfs', '2026-06-16 18:24:25'),
(49, 7, 50, NULL, 'ORD-1781636224', 'pagado', 'minorista', 90900.00, 0.00, 15000.00, 105900.00, 'DFSDFSDFS', '2026-06-16 18:57:04'),
(50, 7, 51, NULL, 'ORD-1781729424', 'enviado', 'minorista', 454500.00, 0.00, 0.00, 454500.00, 'dfsdsfdfs', '2026-06-17 20:50:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_items`
--

CREATE TABLE `orden_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `variante_id` bigint(20) UNSIGNED NOT NULL,
  `lona_id_snapshot` bigint(20) UNSIGNED DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total_linea` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden_items`
--

INSERT INTO `orden_items` (`id`, `orden_id`, `variante_id`, `lona_id_snapshot`, `cantidad`, `precio_unitario`, `total_linea`) VALUES
(54, 50, 45, NULL, 5, 90900.00, 454500.00);

--
-- Disparadores `orden_items`
--
DELIMITER $$
CREATE TRIGGER `trg_descuento_stock_venta` BEFORE INSERT ON `orden_items` FOR EACH ROW BEGIN
        DECLARE stock_actual INT;
        DECLARE lona_ref BIGINT;
        DECLARE talla_ref VARCHAR(10);

        -- Obtener lona y talla de la variante
        SELECT lona_id, talla 
        INTO lona_ref, talla_ref
        FROM variantes_producto
        WHERE id = NEW.variante_id
        LIMIT 1;

        -- Validar que exista
        IF lona_ref IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Variante sin lona asociada';
        END IF;

        -- Obtener stock actual
        SELECT cantidad 
        INTO stock_actual
        FROM lona_tallas
        WHERE lona_id = lona_ref
        AND talla = talla_ref
        LIMIT 1;

        -- Validar existencia
        IF stock_actual IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No existe stock para esa talla';
        END IF;

        -- Validar stock suficiente
        IF stock_actual < NEW.cantidad THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stock insuficiente';
        END IF;

        -- Descontar stock en lona_tallas
        UPDATE lona_tallas
        SET cantidad = cantidad - NEW.cantidad
        WHERE lona_id = lona_ref
        AND talla = talla_ref;

        -- NUEVO: Sincronizar descontando stock también en la variante_producto
        UPDATE variantes_producto
        SET stock = stock - NEW.cantidad
        WHERE id = NEW.variante_id;

        -- Auditoría
        INSERT INTO historial_lonas (
            lona_id,
            orden_item_id,
            accion,
            talla,
            cantidad_cambio,
            cantidad_restante,
            notas
        )
        VALUES (
            lona_ref,
            NULL,
            'descuento',
            talla_ref,
            (NEW.cantidad * -1),
            stock_actual - NEW.cantidad,
            CONCAT('Venta Orden #', NEW.orden_id, ' | Variante ', NEW.variante_id)
        );
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `metodo` varchar(50) DEFAULT NULL,
  `referencia_pasarela` varchar(100) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado','reembolsado') DEFAULT 'pendiente',
  `monto` decimal(10,2) NOT NULL,
  `pagado_en` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `orden_id`, `metodo`, `referencia_pasarela`, `estado`, `monto`, `pagado_en`) VALUES
(34, 47, 'paypal', '55H99214F9160612A', 'aprobado', 200400.00, NULL),
(35, 48, 'paypal', '4SN2573143867344S', 'aprobado', 9180000.00, NULL),
(36, 49, 'paypal', '8GT51441YF5338340', 'aprobado', 105900.00, NULL),
(37, 50, 'paypal', '7FE854883T5454625', 'aprobado', 454500.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '5a455b774a376fde7e1917889f4baa36d212873bf0ab6950c6ef2fe25c127ecc', '[\"*\"]', NULL, NULL, '2026-04-24 19:10:54', '2026-04-24 19:10:54'),
(2, 'App\\Models\\User', 1, 'auth_token', '4a6e62faf36a4963a2a93a37c620760f24d228e9f7d5c0873e80d5454b62b550', '[\"*\"]', '2026-05-06 19:34:04', NULL, '2026-05-06 17:35:04', '2026-05-06 19:34:04'),
(4, 'App\\Models\\Usuario', 2, 'auth_token', '390116a454a355cdf847e4dba388e5508251ea4f0f051399656a7c3c09b2ecbf', '[\"*\"]', NULL, NULL, '2026-06-08 23:08:13', '2026-06-08 23:08:13'),
(5, 'App\\Models\\Usuario', 3, 'auth_token', 'd1f80ac44a18c6a567e9f75e5926fe116a02bbeadcaade80c228fc548ee4e10a', '[\"*\"]', NULL, NULL, '2026-06-08 23:11:04', '2026-06-08 23:11:04'),
(6, 'App\\Models\\Usuario', 2, 'auth_token', '93e25dd76e5f45114753f9fb24cd6aa390c5084115a6c8c75f1c213436480d15', '[\"*\"]', NULL, NULL, '2026-06-08 23:11:52', '2026-06-08 23:11:52'),
(7, 'App\\Models\\Usuario', 2, 'auth_token', '1c52852add37826e672ac579c63cf59bbbf0d2b32adb85a381742b46c505f1d0', '[\"*\"]', NULL, NULL, '2026-06-08 23:14:03', '2026-06-08 23:14:03'),
(15, 'App\\Models\\Usuario', 7, 'auth_token', 'cef2810b473acd9ea9ddb2eb56032bde056770b4aab49bb4742a9ae41a4d4851', '[\"*\"]', '2026-06-09 02:11:31', NULL, '2026-06-09 01:10:44', '2026-06-09 02:11:31'),
(17, 'App\\Models\\Usuario', 7, 'auth_token', 'ddc743320bc83b2f408c685373fd406351a19823c19112afe07f2b3dc2197502', '[\"*\"]', '2026-06-09 02:36:36', NULL, '2026-06-09 02:36:31', '2026-06-09 02:36:36'),
(23, 'App\\Models\\Usuario', 7, 'auth_token', '050c2759b3c15c1ca14a2bf7c45473e0fd264357fa230be1dea1d486e9b8ffb4', '[\"*\"]', NULL, NULL, '2026-06-09 19:46:29', '2026-06-09 19:46:29'),
(31, 'App\\Models\\Usuario', 7, 'auth_token', '338fce648cb8208022b024f4e8b3abd1a726ab195915066ba304939d40d5cf50', '[\"*\"]', '2026-06-11 06:16:28', NULL, '2026-06-11 05:55:40', '2026-06-11 06:16:28'),
(32, 'App\\Models\\Usuario', 7, 'auth_token', '0fe977c48f79589968c104cb2988392d2b977901b8a9c1f95559bb3f4cd9fb40', '[\"*\"]', '2026-06-11 07:11:50', NULL, '2026-06-11 07:00:50', '2026-06-11 07:11:50'),
(33, 'App\\Models\\Usuario', 7, 'test_debug', 'a2b34be97c8b106f409d50bb7caaef4bf21c2c904660e33e0ffc27e1dde9c439', '[\"*\"]', '2026-06-12 01:45:09', NULL, '2026-06-12 01:44:57', '2026-06-12 01:45:09'),
(34, 'App\\Models\\Usuario', 7, 'auth_token', 'da061ec3f812b6bb57f850776647bb87237cddc3f98c5412db1852dd271ed2a9', '[\"*\"]', '2026-06-12 02:57:59', NULL, '2026-06-12 01:46:41', '2026-06-12 02:57:59'),
(35, 'App\\Models\\Usuario', 7, 'auth_token', '2ca4f23179ca8977766a5a9a3d3c321bd26179c5a76ed1a91ce05e020a8a5ec0', '[\"*\"]', '2026-06-12 03:17:19', NULL, '2026-06-12 03:14:52', '2026-06-12 03:17:19'),
(36, 'App\\Models\\Usuario', 7, 'auth_token', '46b1c1c0d8628de109c4ff06d61516b72231f5ebe95abedcdd2c200faa5ef9a1', '[\"*\"]', NULL, NULL, '2026-06-12 05:10:54', '2026-06-12 05:10:54'),
(38, 'App\\Models\\Usuario', 7, 'auth_token', 'dcbbd7090f6e56324f47df5acb60c00cf0059c420d7166239daf3980430be11e', '[\"*\"]', NULL, NULL, '2026-06-12 05:27:14', '2026-06-12 05:27:14'),
(40, 'App\\Models\\Usuario', 7, 'auth_token', '299f8993777de3010531b8f484d9362de1f5165d472b4ee5bdb2f2751bee9817', '[\"*\"]', NULL, NULL, '2026-06-12 05:53:14', '2026-06-12 05:53:14'),
(41, 'App\\Models\\Usuario', 7, 'auth_token', 'a6c4c11b8cb15ee028afa887c54dd1d8f3b09ecf4a2e05e0adf782e2445afbcf', '[\"*\"]', NULL, NULL, '2026-06-12 05:55:03', '2026-06-12 05:55:03'),
(43, 'App\\Models\\Usuario', 7, 'auth_token', 'f92033084654067cf368dd01c62a516b4c5a9a675cfa7a7b49c76e23fb4d01f3', '[\"*\"]', NULL, NULL, '2026-06-12 06:01:12', '2026-06-12 06:01:12'),
(47, 'App\\Models\\Usuario', 7, 'auth_token', '7ab90f21bc7117b56c7472c6dec1f939afae19ad8c8d9ecbb8cb92a0535c4fd0', '[\"*\"]', NULL, NULL, '2026-06-12 06:09:55', '2026-06-12 06:09:55'),
(49, 'App\\Models\\Usuario', 7, 'auth_token', 'd836f1c82b3e5b2c944406ff5877f8f58128fd35240caa9bd0be9c9dcfe9ff98', '[\"*\"]', NULL, NULL, '2026-06-12 06:13:19', '2026-06-12 06:13:19'),
(52, 'App\\Models\\Usuario', 7, 'auth_token', '13088d145e03031b5bfde3aac19a93fb3260d84bad5aa0757b438052792e1968', '[\"*\"]', '2026-06-13 01:04:45', NULL, '2026-06-12 23:39:09', '2026-06-13 01:04:45'),
(53, 'App\\Models\\Usuario', 7, 'auth_token', 'bd03a2b0ad0bbcddf062a85a0123943c182af43bed7245b07e99a794b0bf35a2', '[\"*\"]', '2026-06-13 00:59:21', NULL, '2026-06-13 00:49:29', '2026-06-13 00:59:21'),
(54, 'App\\Models\\Usuario', 7, 'auth_token', 'd6a2a8387e315e2399fbf12a05cc1330a171a278d89f4ec79f940ababa94b515', '[\"*\"]', '2026-06-14 20:05:19', NULL, '2026-06-13 00:59:55', '2026-06-14 20:05:19'),
(59, 'App\\Models\\Usuario', 7, 'auth_token', '07dec50e066777c7858296c20606a28a2b9a5498cb2991973f3911292c809594', '[\"*\"]', '2026-06-16 23:58:59', NULL, '2026-06-16 23:42:17', '2026-06-16 23:58:59'),
(64, 'App\\Models\\Usuario', 7, 'auth_token', 'f70190e6d90871dbf2b27a41a856289e215879f2c01586eedf41e3b08ea262ea', '[\"*\"]', '2026-06-18 02:03:23', NULL, '2026-06-18 01:39:56', '2026-06-18 02:03:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `categoria_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_minorista` decimal(10,2) NOT NULL,
  `precio_mayorista` decimal(10,2) NOT NULL,
  `min_cantidad_mayorista` int(11) DEFAULT 12,
  `publicado` tinyint(1) DEFAULT 0,
  `permitir_sin_stock` tinyint(1) DEFAULT 1,
  `eliminado_en` timestamp NULL DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `destacado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `slug`, `descripcion`, `precio_minorista`, `precio_mayorista`, `min_cantidad_mayorista`, `publicado`, `permitir_sin_stock`, `eliminado_en`, `creado_en`, `destacado`) VALUES
(24, 35, 'Camisa sena', 'camisa-sena', 'La mejor camisa', 100000.00, 80000.00, 12, 1, 1, NULL, '2026-06-17 20:46:53', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1oGvAYmQ38LSAucfixgdVLtxhXZXWCXUT59wgOKR', NULL, '127.0.0.1', 'Thunder Client (https://www.thunderclient.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidkJycFZnN3pJUnN6UDNwWThPYlpON2MxUjRDRWdzZnprZ3dKbkFzYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779476638),
('5AoLDsJeeD6MCuqfZ51mCcDByA49mUYuRtpn1i0U', NULL, '127.0.0.1', 'Thunder Client (https://www.thunderclient.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHpscW44STA2UDY2WU1VaWptSEtiMEh2a2lyUnZJTXhsVFc1blZuNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779821134),
('buGLdwgWdOix7E2R7YqFPDQOb66r4ux6e0IrjYic', NULL, '127.0.0.1', 'Thunder Client (https://www.thunderclient.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTGprUldiWmtBZUFUU0hDc0pyYzVhR2pYN2V1S2U5aFBjdmxuVVczYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779476949),
('CdSZlZ4MUrIeif9EamOS97U50kd2JVn9TMeyxdK0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.121.0 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWW15OElSQXRmS2Voc0s0Snd0dWZZeFVTT3NtTzZDenVYUUtoRkRqVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779826146),
('CInKXL6WI8toFa6OeiIo97Jdc9jXNfe3MkK0xSKG', NULL, '172.18.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 OPR/130.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTE9RZllNWU9zSTZlQkdpN0xMRVpXZlJPeXpwNEp3aHV1VDFnaFZVYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777054958),
('DGovnC562AlH43dm12VrVAE7bH60mmwVjy0WYaVY', NULL, '127.0.0.1', 'Thunder Client (https://www.thunderclient.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieGpzWTNyVjUzVEVmOThhY0RaV0F2elR2MXc3WjlWbGpKalBTYmJJOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779314413),
('DWxzrtBH8KLunutJIxPpHGFwxJiFxOAqoriIzcTL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic2NjWGxmMUNGYmE1UUYxeG15MkFZZUNkanh1VDRoN0FwdnNhTVNjMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780943948),
('FPmjQnISeXQYigScdUi4dgZmPS15HYu8o7QAZNJx', NULL, '127.0.0.1', 'Thunder Client (https://www.thunderclient.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMngyb3hObk9TYUtVclZZdTRYWlFDUE1VMW1oZ09nOE11cm5PRjhPTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779906543),
('HSQSi9aGkm3alBhJRZhTVtgvBz4jXVthdOUL4xMY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; es-CO) WindowsPowerShell/5.1.26100.8457', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkN0NHJYM2lpUDhWV3dVcjJ3b2hsSndEakJVR1JsNVN3T3F6QmdndCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781122168),
('oSuZ37wPOTqv9vrm4RbWx4jKwSSg5Rn1iC33kgWv', NULL, '127.0.0.1', 'Thunder Client (https://www.thunderclient.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXQ0SGtsZjlVOUxJOHppUUlKeWhrS0NaM3JIR3l6RVZGNzZzVGxQUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779914554),
('PTtmOe8yU079uwauZFC8bSQVf1VmwA9mWoAAld4n', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.121.0 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS3pub281azZ5S3RQOHRuS3FuRkhCcEh6TUhURXQzSHhnS0RpWUZmTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779817446),
('rKq5CZ6VeVHhro5wIWVwF2L2nXoCHxw1lDHHeXpD', NULL, '172.18.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 OPR/130.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUt2YUlIRFcxeWpJdmtiV0F2aFduaEZTQTRBTWdHS3BIcTB0NEh6YiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776892464),
('S1O837tz1gQB2LRQQPucdKvH0EH2xEEj5kzbr5na', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.121.0 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNDR1YmNYS3lhQ2cwUHR4UzZKd3hwY2tjU1RUNWdVbzlwMjhzV2hLZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779745625),
('sKHYM32OJMTjWlFfRQ4cbzN42o9rfqseS8wGV11f', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.121.0 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialAzSWluR21nSzFqN2ZtcVZaQXdJY3hrM0VxTEpQNHQ3NlpSQnhzYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779906312),
('u7U6z70U4007juVikRYtRt3J8ZEe99aaYdqPK0VD', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.120.0 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidDRYZ0RaNmRpNkpQcnpmejQ3SVB1WTN2MVdWOXJIaDhLTUZkTVZhSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779314405),
('Uo6drstSYmv5zXWtgzwdIfSYSfQykj2IlkRp0UH5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.121.0 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVE14VWx5UndwcmtsY0dZbjM5VmhENGY0WDJLQ1BMdEUxTWpjVlJWYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779475209),
('UYX2dZmrDzNKaAYKcEfaPnI258IspgNysi40T8jr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.121.0 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUxuUHplWkJBYU1ZTDlreUlkTGd0cWQwU01jTGhqVHVtc0NBNnJWayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779914535),
('v1UAeVAzyVDtqqiEEj7Mp0lWWHKt3EMHCUtXmuvn', NULL, NULL, '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRWtGNUhycXlSeXBrUDNDWFN6bUdiVHZsNXVMNUlzb3o2OVFoY3pEMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODoiaHR0cDovLzoiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781289385),
('v5kzK9XYP30mqhogYR2FV9vHjwyxBhO8Fit3h5EW', NULL, '172.18.0.1', 'Thunder Client (https://www.thunderclient.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ1dOTjM4amlIS2haaUlQa1R0UjZON3FDS2tvR2FWdnRhSGFESjltWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778091147),
('w9RSKYEKiTt7qp0UIAA6HAGGrhFPhlLcOcIyc9y4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.122.1 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNW5HR2Vaa1NrekwwY3J4T1M5Q1hJY2FuVXI2T2NQV0Zpb1VDaDFTSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780423671);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_restablecimiento_password`
--

CREATE TABLE `tokens_restablecimiento_password` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(180) NOT NULL,
  `token` varchar(255) NOT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) DEFAULT NULL COMMENT 'NULL si usa Google OAuth',
  `google_id` varchar(100) DEFAULT NULL,
  `rol` enum('cliente','admin','super_admin') DEFAULT 'cliente',
  `telefono` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `email_verificado_en` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `eliminado_en` timestamp NULL DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `google_id`, `rol`, `telefono`, `avatar_url`, `email_verificado_en`, `remember_token`, `eliminado_en`, `creado_en`, `actualizado_en`) VALUES
(1, 'Alejandro', 'test@gmail.com', '$2y$12$jaaZNDO.XKDO432O90tRG.WWoSqjdPgBa1OgSetWo89.otsoER6DG', NULL, 'cliente', NULL, NULL, NULL, NULL, NULL, '2026-04-24 19:10:54', '2026-04-24 19:10:54'),
(7, 'Jeffrey hermoso hola', 'jeffrey232008suarez@gmail.com', '$2y$12$uvy3tni7IJBiTpd33tIXh.xvKToSWLpkSs3lwuTMwwg75KGCpG/4m', NULL, 'super_admin', '111111', NULL, '2026-06-11 00:59:16', NULL, NULL, '2026-06-08 20:10:14', '2026-06-17 19:52:26'),
(22, 'isa', 'isaromera750@gmai.com', '$2y$12$CXKtZqhHp0LU/a97zSLrfuq4rJkj9yu5YhxXlzYSTCNhFPY3efHYy', NULL, 'cliente', '3243244342', NULL, NULL, NULL, NULL, '2026-06-16 18:02:24', '2026-06-16 18:02:24'),
(23, 'miguelito', 'miguelito3er@gmail.com', '$2y$12$Ry3dJneyrJv77NjA6.MOi.UqwOb8vDcyV.rOgMMwxehXozn2NY7AW', NULL, 'cliente', '2324342342', NULL, NULL, NULL, NULL, '2026-06-16 18:26:04', '2026-06-16 18:26:04'),
(24, 'hector', 'hmaya10@hotmail.com', '$2y$12$4/GNv5DM4GTirQLqjv8X0OKfEhFC/CAjqDVPwigs4E3OnRe6jEgYO', NULL, 'cliente', '3434243243', NULL, '2026-06-16 18:40:54', NULL, NULL, '2026-06-16 18:38:53', '2026-06-16 18:40:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variantes_producto`
--

CREATE TABLE `variantes_producto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `lona_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `color_hex` varchar(20) DEFAULT NULL,
  `talla` varchar(10) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `precio_extra` decimal(10,2) DEFAULT 0.00,
  `descuento` int(11) DEFAULT 0,
  `eliminado_en` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `variantes_producto`
--

INSERT INTO `variantes_producto` (`id`, `producto_id`, `lona_id`, `sku`, `color`, `color_hex`, `talla`, `stock`, `precio_extra`, `descuento`, `eliminado_en`) VALUES
(45, 24, 11, 'CAMISASENA', '#000000', '#000000', 'XS', 0, 1000.00, 10, NULL),
(46, 24, 11, 'CAMISASENA', '#000000', '#000000', 'S', 5, 1000.00, 10, NULL),
(47, 24, 11, 'CAMISASENA', '#000000', '#000000', 'M', 6, 3000.00, 40, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carrito_usuario` (`usuario_id`),
  ADD KEY `cupon_id` (`cupon_id`);

--
-- Indices de la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carrito` (`carrito_id`,`variante_id`),
  ADD KEY `variante_id` (`variante_id`),
  ADD KEY `idx_carrito` (`carrito_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `padre_id` (`padre_id`);

--
-- Indices de la tabla `configuraciones_cms`
--
ALTER TABLE `configuraciones_cms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`),
  ADD KEY `actualizado_por` (`actualizado_por`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cupones`
--
ALTER TABLE `cupones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_id` (`orden_id`),
  ADD KEY `resuelto_por` (`resuelto_por`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `dotaciones`
--
ALTER TABLE `dotaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `envios`
--
ALTER TABLE `envios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_envio_orden` (`orden_id`),
  ADD KEY `idx_estado_envio` (`estado`);

--
-- Indices de la tabla `historial_lonas`
--
ALTER TABLE `historial_lonas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lona_id` (`lona_id`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `variante_id` (`variante_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lonas`
--
ALTER TABLE `lonas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `dotacion_id` (`dotacion_id`),
  ADD KEY `lonas_categoria_id_foreign` (`categoria_id`);

--
-- Indices de la tabla `lona_tallas`
--
ALTER TABLE `lona_tallas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lona_id` (`lona_id`,`talla`),
  ADD KEY `idx_lona_talla` (`lona_id`,`talla`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `confirmado_por` (`confirmado_por`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `cupon_id` (`cupon_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_direccion` (`direccion_id`);

--
-- Indices de la tabla `orden_items`
--
ALTER TABLE `orden_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orden` (`orden_id`),
  ADD KEY `idx_variante` (`variante_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pago_orden` (`orden_id`),
  ADD KEY `idx_estado_pago` (`estado`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `categoria_id` (`categoria_id`);
ALTER TABLE `productos` ADD FULLTEXT KEY `nombre` (`nombre`,`descripcion`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tokens_restablecimiento_password`
--
ALTER TABLE `tokens_restablecimiento_password`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD KEY `idx_usuarios_rol` (`rol`);

--
-- Indices de la tabla `variantes_producto`
--
ALTER TABLE `variantes_producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_id` (`producto_id`,`color`,`talla`),
  ADD KEY `idx_filtros` (`color`,`talla`,`stock`),
  ADD KEY `idx_lona` (`lona_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carritos`
--
ALTER TABLE `carritos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `configuraciones_cms`
--
ALTER TABLE `configuraciones_cms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cupones`
--
ALTER TABLE `cupones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `dotaciones`
--
ALTER TABLE `dotaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `envios`
--
ALTER TABLE `envios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historial_lonas`
--
ALTER TABLE `historial_lonas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lonas`
--
ALTER TABLE `lonas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `lona_tallas`
--
ALTER TABLE `lona_tallas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `orden_items`
--
ALTER TABLE `orden_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `tokens_restablecimiento_password`
--
ALTER TABLE `tokens_restablecimiento_password`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `variantes_producto`
--
ALTER TABLE `variantes_producto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD CONSTRAINT `carritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carritos_ibfk_2` FOREIGN KEY (`cupon_id`) REFERENCES `cupones` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  ADD CONSTRAINT `carrito_items_ibfk_1` FOREIGN KEY (`carrito_id`) REFERENCES `carritos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_items_ibfk_2` FOREIGN KEY (`variante_id`) REFERENCES `variantes_producto` (`id`);

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`padre_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `configuraciones_cms`
--
ALTER TABLE `configuraciones_cms`
  ADD CONSTRAINT `configuraciones_cms_ibfk_1` FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD CONSTRAINT `devoluciones_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`),
  ADD CONSTRAINT `devoluciones_ibfk_2` FOREIGN KEY (`resuelto_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `envios`
--
ALTER TABLE `envios`
  ADD CONSTRAINT `envios_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`);

--
-- Filtros para la tabla `historial_lonas`
--
ALTER TABLE `historial_lonas`
  ADD CONSTRAINT `historial_lonas_ibfk_1` FOREIGN KEY (`lona_id`) REFERENCES `lonas` (`id`),
  ADD CONSTRAINT `historial_lonas_ibfk_2` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  ADD CONSTRAINT `imagenes_producto_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `imagenes_producto_ibfk_2` FOREIGN KEY (`variante_id`) REFERENCES `variantes_producto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lonas`
--
ALTER TABLE `lonas`
  ADD CONSTRAINT `lonas_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lonas_ibfk_1` FOREIGN KEY (`dotacion_id`) REFERENCES `dotaciones` (`id`);

--
-- Filtros para la tabla `lona_tallas`
--
ALTER TABLE `lona_tallas`
  ADD CONSTRAINT `lona_tallas_ibfk_1` FOREIGN KEY (`lona_id`) REFERENCES `lonas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `notificaciones_ibfk_2` FOREIGN KEY (`confirmado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD CONSTRAINT `ordenes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ordenes_ibfk_2` FOREIGN KEY (`direccion_id`) REFERENCES `direcciones` (`id`),
  ADD CONSTRAINT `ordenes_ibfk_3` FOREIGN KEY (`cupon_id`) REFERENCES `cupones` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `orden_items`
--
ALTER TABLE `orden_items`
  ADD CONSTRAINT `orden_items_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orden_items_ibfk_2` FOREIGN KEY (`variante_id`) REFERENCES `variantes_producto` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `variantes_producto`
--
ALTER TABLE `variantes_producto`
  ADD CONSTRAINT `variantes_producto_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variantes_producto_ibfk_2` FOREIGN KEY (`lona_id`) REFERENCES `lonas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
