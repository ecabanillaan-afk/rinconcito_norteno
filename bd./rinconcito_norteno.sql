-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 24-06-2026 a las 20:10:28
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rinconcito_norteno`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombres`, `telefono`, `direccion`) VALUES
(1, 'Lisbetttt', '923242332', 'av central'),
(3, 'Erickson Cabanillas', '914814369', 'av puno , calle los pinos'),
(4, 'Miguel Sanchez', '912321333', 'av. Alameno ft a santa luisa'),
(5, 'Juan', '932131233', 'av san juan ft a av castillo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_orden`
--

CREATE TABLE `detalle_orden` (
  `id` int(11) NOT NULL,
  `orden_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `estado_kitchen` varchar(50) NOT NULL DEFAULT 'Pendiente',
  `estado_cocina` varchar(50) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_orden`
--

INSERT INTO `detalle_orden` (`id`, `orden_id`, `producto_id`, `cantidad`, `precio`, `subtotal`, `estado_kitchen`, `estado_cocina`) VALUES
(1, 1, 3, 2, 35.00, 70.00, 'Notificado', 'Pendiente'),
(2, 1, 1, 2, 25.00, 50.00, 'Notificado', 'Pendiente'),
(3, 1, 1, 2, 25.00, 50.00, 'Notificado', 'Pendiente'),
(4, 1, 1, 2, 25.00, 50.00, 'Notificado', 'Pendiente'),
(5, 1, 1, 1, 25.00, 25.00, 'Notificado', 'Pendiente'),
(6, 1, 1, 1, 25.00, 25.00, 'Notificado', 'Pendiente'),
(7, 1, 1, 1, 25.00, 25.00, 'Notificado', 'Pendiente'),
(8, 1, 1, 1, 25.00, 25.00, 'Notificado', 'Pendiente'),
(9, 1, 14, 1, 4.00, 4.00, 'Pendiente', 'Pendiente'),
(10, 2, 15, 1, 8.00, 8.00, 'Pendiente', 'Pendiente'),
(11, 2, 7, 1, 28.00, 28.00, 'Pendiente', 'Pendiente'),
(12, 3, 13, 1, 12.00, 12.00, 'Pendiente', 'Pendiente'),
(13, 4, 5, 2, 45.00, 90.00, 'Notificado', 'Pendiente'),
(14, 3, 22, 1, 8.00, 8.00, 'Pendiente', 'Pendiente'),
(15, 5, 8, 1, 30.00, 30.00, 'Notificado', 'Pendiente'),
(16, 6, 3, 2, 35.00, 70.00, 'Notificado', 'Listo'),
(17, 1, 15, 1, 8.00, 8.00, 'Pendiente', 'Pendiente'),
(18, 7, 4, 2, 40.00, 80.00, 'Notificado', 'Listo'),
(19, 8, 7, 1, 28.00, 28.00, 'Notificado', 'Listo'),
(20, 9, 1, 1, 25.00, 25.00, 'Pendiente', 'Pendiente'),
(21, 9, 10, 4, 15.00, 60.00, 'Pendiente', 'Pendiente'),
(22, 10, 1, 1, 25.00, 25.00, 'Pendiente', 'Listo'),
(23, 0, 2, 2, 30.00, 60.00, 'Pendiente', 'Pendiente'),
(24, 0, 15, 1, 8.00, 8.00, 'Pendiente', 'Pendiente'),
(25, 0, 1, 1, 25.00, 25.00, 'Pendiente', 'Pendiente'),
(26, 0, 5, 2, 45.00, 90.00, 'Pendiente', 'Pendiente'),
(27, 0, 9, 1, 35.00, 35.00, 'Pendiente', 'Pendiente'),
(28, 0, 13, 1, 12.00, 12.00, 'Pendiente', 'Pendiente'),
(29, 11, 5, 1, 45.00, 45.00, 'Notificado', 'Listo'),
(30, 12, 3, 1, 35.00, 35.00, 'Notificado', 'Listo'),
(31, 12, 17, 1, 10.00, 10.00, 'Pendiente', 'Pendiente'),
(32, 13, 1, 1, 25.00, 25.00, 'Notificado', 'Listo'),
(33, 14, 2, 1, 30.00, 30.00, 'Notificado', 'Listo'),
(34, 15, 1, 1, 25.00, 25.00, 'Pendiente', 'Pendiente'),
(35, 16, 7, 1, 28.00, 28.00, 'Notificado', 'Listo'),
(36, 16, 2, 1, 30.00, 30.00, 'Notificado', 'Listo'),
(37, 17, 5, 1, 45.00, 45.00, 'Notificado', 'Listo'),
(38, 18, 1, 1, 25.00, 25.00, 'Notificado', 'Listo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'Libre'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `numero`, `estado`) VALUES
(1, 1, 'Ocupada'),
(2, 2, 'Ocupada'),
(3, 3, 'Ocupada'),
(4, 4, 'Libre'),
(5, 5, 'Libre'),
(6, 6, 'Libre'),
(7, 7, 'Ocupada'),
(8, 8, 'Ocupada'),
(9, 9, 'Ocupada'),
(10, 10, 'Libre'),
(11, 11, 'Libre'),
(12, 12, 'Libre'),
(13, 13, 'Libre'),
(14, 14, 'Libre'),
(15, 15, 'Libre'),
(16, 16, 'Libre'),
(17, 17, 'Libre'),
(18, 18, 'Libre'),
(19, 19, 'Ocupada'),
(20, 20, 'Ocupada'),
(99, 99, 'Libre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_mesa`
--

CREATE TABLE `ordenes_mesa` (
  `id` int(11) NOT NULL,
  `mesa_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes_mesa`
--

INSERT INTO `ordenes_mesa` (`id`, `mesa_id`, `fecha`, `estado`) VALUES
(1, 1, '2026-06-01 18:53:20', 'Abierta'),
(2, 2, '2026-06-01 19:28:56', 'Cerrada'),
(3, 2, '2026-06-01 19:31:27', 'Cerrada'),
(4, 4, '2026-06-01 20:01:42', 'Cerrada'),
(5, 7, '2026-06-01 23:44:26', 'Cerrada'),
(6, 7, '2026-06-02 09:45:37', 'Abierta'),
(7, 9, '2026-06-02 23:18:19', 'Abierta'),
(8, 4, '2026-06-02 23:19:54', 'Cerrada'),
(9, 3, '2026-06-24 08:06:15', 'Cerrada'),
(10, 5, '2026-06-24 08:06:45', 'Cerrada'),
(11, 99, '2026-06-24 10:20:22', 'Abierta'),
(12, 20, '2026-06-24 10:27:11', 'Abierta'),
(13, 19, '2026-06-24 10:49:05', 'Abierta'),
(14, 99, '2026-06-24 10:50:04', 'Abierta'),
(15, 3, '2026-06-24 11:15:19', 'Cerrada'),
(16, 8, '2026-06-24 12:03:03', 'Abierta'),
(17, 2, '2026-06-24 12:56:39', 'Abierta'),
(18, 3, '2026-06-24 13:00:44', 'Abierta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `tipo_envio` varchar(50) DEFAULT 'Delivery',
  `costo_delivery` decimal(10,2) DEFAULT 0.00,
  `metodo_pago` varchar(50) DEFAULT 'Efectivo',
  `estado_pedido` varchar(50) DEFAULT 'En proceso'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `fecha`, `total`, `tipo_envio`, `costo_delivery`, `metodo_pago`, `estado_pedido`) VALUES
(1, 3, '2026-06-24 09:46:18', 73.00, 'Delivery', 5.00, 'Yape', 'Pedido entregado'),
(2, 1, '2026-06-24 09:53:13', 30.00, 'Delivery', 5.00, 'Tarjeta', 'Pedido entregado'),
(3, 5, '2026-06-24 10:06:15', 140.00, 'Delivery', 3.00, 'Efectivo', 'Pedido entregado'),
(4, 5, '2026-06-24 10:12:32', 30.00, 'Delivery', 5.00, 'Efectivo', 'Pedido entregado'),
(5, 4, '2026-06-24 10:20:22', 48.00, 'Delivery', 3.00, 'Efectivo', 'Pedido entregado'),
(6, 5, '2026-06-24 10:50:04', 35.00, 'Delivery', 5.00, 'Yape', 'En proceso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `estado` varchar(50) NOT NULL DEFAULT 'Activo',
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `categoria`, `precio`, `stock`, `estado`, `imagen`) VALUES
(1, 'Chicharrón de Pescado', 'Pescados y Mariscos', 25.00, 48, 'Activo', 'chicharron.png'),
(2, 'Cabrilla Frita con Yuca', 'Pescados y Mariscos', 30.00, 48, 'Activo', 'Cabrilla.png'),
(3, 'Chicharrón Mixto', 'Pescados y Mariscos', 35.00, 50, 'Activo', 'Mixto.png'),
(4, 'Jalea de Pescado + Ceviche de Pescado', 'Pescados y Mariscos', 40.00, 50, 'Activo', 'Jalea.png'),
(5, 'Maretazo', 'Pescados y Mariscos', 45.00, 49, 'Activo', 'Maretazo.png'),
(6, 'Chicharrón + Leche de Tigre', 'Pescados y Mariscos', 30.00, 50, 'Activo', 'Tigre.png'),
(7, 'Arroz con Mariscos', 'Pescados y Mariscos', 28.00, 49, 'Activo', 'ArrozconMariscos.png'),
(8, 'Ceviche de Pescado', 'Pescados y Mariscos', 30.00, 50, 'Activo', 'CevichedePescado.png'),
(9, 'Ceviche Mixto', 'Pescados y Mariscos', 35.00, 50, 'Activo', 'CevicheMixto.png'),
(10, 'Leche de Tigre', 'Pescados y Mariscos', 15.00, 50, 'Activo', 'Leche.png'),
(11, 'Ceviche de Corvina', 'Pescados y Mariscos', 40.00, 50, 'Activo', 'Corvina.png'),
(12, 'Gaseosa Inca Kola 1L', 'Bebidas', 8.00, 100, 'Activo', 'Inka1L.png'),
(13, 'Gaseosa Inca Kola 1.5L', 'Bebidas', 12.00, 100, 'Activo', 'Inka1.5L.png'),
(14, 'Gaseosa Gordita', 'Bebidas', 4.00, 100, 'Activo', 'Gordita.png'),
(15, 'Gaseosa Coca Cola 1L', 'Bebidas', 8.00, 100, 'Activo', 'Coca1L.png'),
(16, 'Gaseosa Coca Cola 1.5L', 'Bebidas', 12.00, 100, 'Activo', 'Coca1.5L.png'),
(17, 'Cerveza Cusqueña Trigo', 'Bebidas', 10.00, 100, 'Activo', 'Trigo.png'),
(18, 'Cerveza Pilsen 1L', 'Bebidas', 12.00, 100, 'Activo', 'Pilsen.png'),
(19, 'Cerveza Cusqueña Negra', 'Bebidas', 10.00, 100, 'Activo', 'Negra.png'),
(20, 'Chicha de Jora', 'Bebidas', 5.00, 100, 'Activo', 'Jora.png'),
(21, 'Chicha Morada', 'Bebidas', 5.00, 100, 'Activo', 'Morada.png'),
(22, 'Limonada Frozen', 'Bebidas', 8.00, 100, 'Activo', 'Frozen.png'),
(23, 'Camello', 'Pescados y Mariscos', 15.00, 4, 'Activo', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `nombre`, `rol`) VALUES
(3, 'admin', '1234', 'Administrador', 'Administrador'),
(4, 'Emesero', '12345', 'Erickson', 'Mesero');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ordenes_mesa`
--
ALTER TABLE `ordenes_mesa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `ordenes_mesa`
--
ALTER TABLE `ordenes_mesa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
