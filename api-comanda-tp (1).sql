-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-07-2024 a las 21:02:13
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
-- Base de datos: `api-comanda-tp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comandas`
--

CREATE TABLE `comandas` (
  `id_comanda` int(11) NOT NULL,
  `id_mesa` int(255) NOT NULL,
  `cliente` varchar(255) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `demora` varchar(250) NOT NULL,
  `fecha` varchar(250) NOT NULL,
  `precio` int(50) NOT NULL,
  `codigo_comanda` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comandas`
--

INSERT INTO `comandas` (`id_comanda`, `id_mesa`, `cliente`, `estado`, `demora`, `fecha`, `precio`, `codigo_comanda`) VALUES
(3, 1, 'matias', 'lista', '0', '2024-07-06', 0, 27612),
(4, 2, 'matias', 'ingresada', '0', '2024-07-07', 1400, 42258);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comanda_productos`
--

CREATE TABLE `comanda_productos` (
  `id_comanda` int(50) NOT NULL,
  `mesa` varchar(250) NOT NULL,
  `id_producto` int(50) NOT NULL,
  `cantidad` int(50) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `puesto` varchar(250) NOT NULL,
  `demora` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comanda_productos`
--

INSERT INTO `comanda_productos` (`id_comanda`, `mesa`, `id_producto`, `cantidad`, `estado`, `puesto`, `demora`) VALUES
(4, '1', 1, 1, 'Sin empezar', 'cocinero', ''),
(5, '1', 2, 1, 'Sin empezar', 'cocinero', ''),
(6, '1', 3, 1, 'Sin empezar', 'cervezero', ''),
(7, '1', 4, 1, 'lista', 'bartender', '5'),
(8, '2', 1, 1, 'Sin empezar', 'cocinero', ''),
(9, '2', 2, 1, 'Sin empezar', 'cocinero', ''),
(10, '2', 3, 1, 'Sin empezar', 'cervezero', ''),
(11, '2', 4, 1, 'Sin empezar', 'bartender', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id_encuesta` int(11) NOT NULL,
  `numero_mesa` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `puntuacion` varchar(250) NOT NULL,
  `codigo_pedido` int(11) NOT NULL,
  `fecha` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id_encuesta`, `numero_mesa`, `nombre`, `puntuacion`, `codigo_pedido`, `fecha`) VALUES
(1, 1, 'matias', '5', 27612, '2024-07-07'),
(2, 1, 'matias', '5', 27612, '2024-07-07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id_mesas` int(11) NOT NULL,
  `max_comensales` varchar(256) NOT NULL,
  `numero_mesa` int(11) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `mozo` varchar(250) NOT NULL,
  `id_puntuacion` int(11) NOT NULL,
  `fecha` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id_mesas`, `max_comensales`, `numero_mesa`, `estado`, `mozo`, `id_puntuacion`, `fecha`) VALUES
(11, '1', 1, 'pagada', 'matiasMozo', 0, '06-07-2024'),
(12, '6', 1, 'cliente esperando pedido', 'matiasMozo', 0, '08-07-2024');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operaciones`
--

CREATE TABLE `operaciones` (
  `nombre` varchar(250) NOT NULL,
  `puesto` varchar(250) NOT NULL,
  `operacion` varchar(250) NOT NULL,
  `fecha_ingreso` varchar(250) NOT NULL,
  `fecha_salida` varchar(250) NOT NULL,
  `cantidad` int(50) NOT NULL,
  `id_operacion` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `operaciones`
--

INSERT INTO `operaciones` (`nombre`, `puesto`, `operacion`, `fecha_ingreso`, `fecha_salida`, `cantidad`, `id_operacion`) VALUES
('matiassk1', 'cocinero', 'Login', '2024-07-01 18:56:41', '', 3, 3),
('matiasSocio', 'socio', 'CargarComanda', '2024-07-01 19:08:29', '', 4, 4),
('Matias', 'Socio', 'BorrarUsuario', '2024-07-01 20:45:30', '', 4, 5),
('matiasSocio', 'socio', 'Login', '2024-07-01 20:52:26', '', 1, 6),
('matiasestudar', 'cocinero', 'Crear Usuario', '2024-07-01 20:52:34', '', 2, 7),
('Matias', 'Socio', 'BorrarComanda', '2024-07-01 21:07:49', '', 6, 8),
('Matias', 'Socio', 'BorrarMesa', '2024-07-01 21:11:29', '', 3, 9),
('matiasestudar', 'cocinero', 'Login', '2024-07-02 00:54:24', '', 2, 10),
('matiasestudar', 'socio', 'Crear Usuario', '2024-07-02 00:58:06', '', 1, 11),
('sociomatias', 'socio', 'Crear Usuario', '2024-07-02 00:58:16', '', 1, 12),
('sociomatias', 'socio', 'Login', '2024-07-02 00:58:31', '', 6, 13),
('matiasMozo', 'mesero', 'Crear Usuario', '2024-07-06 16:53:53', '', 1, 14),
('matiasMozo', 'mesero', 'Login', '2024-07-06 16:54:13', '', 2, 15),
('matiasMozo', 'mesero', 'CargarComanda', '2024-07-06 16:59:18', '', 2, 16),
('matiasCocinero', 'cocinero', 'Login', '2024-07-06 17:19:24', '', 3, 17),
('matiasMozo', 'mesero', 'ModificarComanda', '2024-07-06 17:32:05', '', 4, 18),
('sociomatias', 'socio', 'CargarComanda', '2024-07-07 04:07:43', '', 1, 19),
('Cliente', 'Na', 'CargarEncuesta', '2024-07-07 19:38:07', '', 7, 20),
('matiasCocinero', 'cocinero', 'Crear Usuario', '2024-07-08 20:43:16', '', 2, 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `puesto` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `mail` varchar(250) NOT NULL,
  `fecha_ingreso` varchar(250) NOT NULL,
  `fecha_salida` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `puesto`, `clave`, `estado`, `mail`, `fecha_ingreso`, `fecha_salida`) VALUES
(2, 'sociomatias', 'socio', 'matias1234', 'nada', 'matias.skenen@gmail.com', '02-07-2024', '---'),
(3, 'matiasMozo', 'mesero', 'matias1234', '1', 'matias.skenen@gmail.com', '06-07-2024', '---'),
(7, 'matiasCocinero', 'cocinero', 'matias1234', '1', 'matias.skenen@gmail.com', '08-07-2024', '---');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD PRIMARY KEY (`id_comanda`);

--
-- Indices de la tabla `comanda_productos`
--
ALTER TABLE `comanda_productos`
  ADD PRIMARY KEY (`id_comanda`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id_encuesta`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id_mesas`);

--
-- Indices de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  ADD PRIMARY KEY (`id_operacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comandas`
--
ALTER TABLE `comandas`
  MODIFY `id_comanda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `comanda_productos`
--
ALTER TABLE `comanda_productos`
  MODIFY `id_comanda` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id_encuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id_mesas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  MODIFY `id_operacion` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
