-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-07-2024 a las 23:18:30
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `codigo_mesa` int(11) NOT NULL,
  `max_comensales` varchar(256) NOT NULL,
  `codigo_comanda` int(11) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `mozo` int(11) NOT NULL,
  `id_puntuacion` int(11) NOT NULL,
  `fecha` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('matiasSocio', 'socio', 'CargarComanda', '2024-07-01 19:08:29', '', 3, 4),
('Matias', 'Socio', 'BorrarUsuario', '2024-07-01 20:45:30', '', 3, 5),
('matiasSocio', 'socio', 'Login', '2024-07-01 20:52:26', '', 1, 6),
('matiasestudar', 'cocinero', 'Crear Usuario', '2024-07-01 20:52:34', '', 1, 7),
('Matias', 'Socio', 'BorrarComanda', '2024-07-01 21:07:49', '', 6, 8),
('Matias', 'Socio', 'BorrarMesa', '2024-07-01 21:11:29', '', 3, 9);

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
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`codigo_mesa`);

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
  MODIFY `id_comanda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comanda_productos`
--
ALTER TABLE `comanda_productos`
  MODIFY `id_comanda` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `codigo_mesa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  MODIFY `id_operacion` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
