-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 27-07-2025 a las 17:10:19
-- Versión del servidor: 10.4.18-MariaDB
-- Versión de PHP: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: `comunitaria`
--
CREATE DATABASE IF NOT EXISTS `comunitaria1` DEFAULT CHARACTER SET latin1 COLLATE latin1_spanish_ci;
USE `comunitaria1`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `beneficiarios`
--

CREATE TABLE `beneficiarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `contrasena` char(32) COLLATE latin1_spanish_ci NOT NULL,
  `clase` int(11) NOT NULL DEFAULT 0,
  `nombre` varchar(150) COLLATE latin1_spanish_ci NOT NULL,
  `apellidos` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `direccion` varchar(500) COLLATE latin1_spanish_ci NOT NULL,
  `movil` varchar(20) COLLATE latin1_spanish_ci NOT NULL,
  `correo` varchar(200) COLLATE latin1_spanish_ci NOT NULL,
  `cuenta` int(11) NOT NULL DEFAULT 0,
  `bloqueado` tinyint(4) NOT NULL DEFAULT 0,
  `activo` tinyint(4) NOT NULL DEFAULT 0,
  `transferirILLA` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `recibidoILLA` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `borrado` datetime NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `actualizado` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `id` int(11) NOT NULL,
  `clase` varchar(100) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases_comercio`
--

CREATE TABLE `clases_comercio` (
  `id` int(11) NOT NULL,
  `clase` int(11) NOT NULL,
  `comercio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comercios`
--

CREATE TABLE `comercios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `contrasena` char(32) COLLATE latin1_spanish_ci NOT NULL,
  `nombre` varchar(150) COLLATE latin1_spanish_ci NOT NULL,
  `CIF` varchar(150) COLLATE latin1_spanish_ci NOT NULL,
  `contacto` varchar(300) COLLATE latin1_spanish_ci NOT NULL,
  `direccion` varchar(500) COLLATE latin1_spanish_ci DEFAULT '',
  `movil` varchar(20) COLLATE latin1_spanish_ci DEFAULT '',
  `correo` varchar(200) COLLATE latin1_spanish_ci NOT NULL,
  `coordenadas` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `logo` tinyint(1) NOT NULL DEFAULT 0,
  `hashDatos` char(32) COLLATE latin1_spanish_ci NOT NULL,
  `cuenta` int(11) NOT NULL DEFAULT 0,
  `bloqueado` tinyint(4) NOT NULL DEFAULT 0,
  `activo` tinyint(4) NOT NULL DEFAULT 0,
  `transferirILLA` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `recibidoILLA` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `canjeadoILLA` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `pagadoILLA` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `borrado` datetime NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `actualizado` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `id` int(11) NOT NULL,
  `clave` char(55) COLLATE latin1_spanish_ci NOT NULL,
  `balanceXLM` decimal(17,7) NOT NULL,
  `balanceILLA` decimal(17,7) NOT NULL,
  `creada` tinyint(4) NOT NULL DEFAULT 0,
  `trustline` tinyint(4) NOT NULL DEFAULT 0,
  `autorizada` tinyint(1) NOT NULL DEFAULT 0,
  `bloqueada` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(10) NOT NULL,
  `desc_grupo` varchar(40) COLLATE latin1_spanish_ci NOT NULL,
  `id_perfil` int(10) NOT NULL,
  `id_oficio` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `desc_grupo`, `id_perfil`, `id_oficio`) VALUES
(1, 'Superusuarios', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `euros` decimal(17,7) NOT NULL,
  `de_mes` int(11) NOT NULL,
  `de_ano` int(11) NOT NULL,
  `factura` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `notas` varchar(500) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros`
--

CREATE TABLE `parametros` (
  `clave` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `valor` varchar(10000) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id_prf` int(11) NOT NULL,
  `desc_prf` varchar(30) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `perfiles`
--

INSERT INTO `perfiles` (`id_prf`, `desc_prf`) VALUES
(1, 'Superusuario'),
(2, 'Administrativo'),
(3, 'Gestor de beneficiarios'),
(4, 'Supervisor de cuentas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfilespermisos`
--

CREATE TABLE `perfilespermisos` (
  `id` int(10) NOT NULL,
  `id_prf` int(11) NOT NULL,
  `id_per` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `perfilespermisos`
--

INSERT INTO `perfilespermisos` (`id`, `id_prf`, `id_per`) VALUES
(7, 1, 1),
(8, 1, 2),
(9, 1, 3),
(10, 1, 4),
(11, 2, 2),
(12, 3, 2),
(13, 3, 3),
(14, 4, 2),
(23, 4, 3),
(24, 4, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_per` int(11) NOT NULL,
  `desc_per` varchar(60) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_per`, `desc_per`) VALUES
(1, 'Configuración del portal'),
(2, 'altas benificiarios y comercios'),
(3, 'Pagos, bloqueo de beneficiarios y comercios'),
(4, 'ver datos reservados de beneficiarios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `session_id` varchar(40) COLLATE latin1_spanish_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE latin1_spanish_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_data` text COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `id` int(11) NOT NULL,
  `tipo` int(11) NOT NULL COMMENT '1: pago; 2: cobro',
  `usuario` int(11) NOT NULL COMMENT '1: distribuidora, 2: emisora, >0: otras',
  `tipoUsuario` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Comunitaria, 1: Beneficiario, 2: Comercio',
  `moneda` int(11) NOT NULL COMMENT '0: XLM, 1: Cripto',
  `cantidad` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `de_a_cuenta` char(55) COLLATE latin1_spanish_ci NOT NULL,
  `de_a_tipoUsuario` tinyint(4) NOT NULL DEFAULT 0 COMMENT '	0: Comunitaria, 1: Beneficiario, 2: Comercio	',
  `de_a_usuario` int(11) NOT NULL,
  `momento` datetime NOT NULL DEFAULT current_timestamp(),
  `idTransaccion` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usr` int(10) UNSIGNED NOT NULL,
  `nombre_usr` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `login_usr` varchar(30) COLLATE latin1_spanish_ci NOT NULL,
  `pwd_usr` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `correo` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `token` text COLLATE latin1_spanish_ci NOT NULL,
  `caracteristicas` char(10) COLLATE latin1_spanish_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usr`, `nombre_usr`, `login_usr`, `pwd_usr`, `correo`, `token`, `caracteristicas`) VALUES
(1, 'Administración', 'adm', 'c4ca4238a0b923820dcc509a6f75849b', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_grupos`
--

CREATE TABLE `usuarios_grupos` (
  `id` int(10) NOT NULL,
  `id_usr` int(10) NOT NULL,
  `id_grupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios_grupos`
--

INSERT INTO `usuarios_grupos` (`id`, `id_usr`, `id_grupo`) VALUES
(1, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `beneficiarios`
--
ALTER TABLE `beneficiarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clases_comercio`
--
ALTER TABLE `clases_comercio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clase` (`clase`),
  ADD KEY `comercio` (`comercio`);

--
-- Indices de la tabla `comercios`
--
ALTER TABLE `comercios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `parametros`
--
ALTER TABLE `parametros`
  ADD PRIMARY KEY (`clave`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id_prf`);

--
-- Indices de la tabla `perfilespermisos`
--
ALTER TABLE `perfilespermisos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_per`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indices de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usr`),
  ADD KEY `iLogin` (`login_usr`);

--
-- Indices de la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `beneficiarios`
--
ALTER TABLE `beneficiarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clases_comercio`
--
ALTER TABLE `clases_comercio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comercios`
--
ALTER TABLE `comercios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id_prf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `perfilespermisos`
--
ALTER TABLE `perfilespermisos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clases_comercio`
--
ALTER TABLE `clases_comercio`
  ADD CONSTRAINT `clases_comercio_ibfk_1` FOREIGN KEY (`clase`) REFERENCES `clases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;
