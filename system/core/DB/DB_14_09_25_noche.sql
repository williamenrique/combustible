-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para combustible_db
CREATE DATABASE IF NOT EXISTS `combustible_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `combustible_db`;

-- Volcando estructura para tabla combustible_db.table_departamentos
CREATE TABLE IF NOT EXISTS `table_departamentos` (
  `departamento_id` int(11) NOT NULL AUTO_INCREMENT,
  `departamento_nombre` varchar(100) NOT NULL,
  `departamento_status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`departamento_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_departamentos: ~3 rows (aproximadamente)
INSERT IGNORE INTO `table_departamentos` (`departamento_id`, `departamento_nombre`, `departamento_status`) VALUES
	(1, 'Sistemas', 1),
	(2, 'Ventas', 1),
	(3, 'Administración', 1);

-- Volcando estructura para tabla combustible_db.table_es_cierre
CREATE TABLE IF NOT EXISTS `table_es_cierre` (
  `id_cierre` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `fecha_cierre` varchar(10) DEFAULT NULL,
  `hora_cierre` varchar(10) DEFAULT NULL,
  `total_efectivob` varchar(10) DEFAULT NULL,
  `total_efectivod` int(11) DEFAULT NULL,
  `total_tarjeta` varchar(10) DEFAULT NULL,
  `tasa_dia` varchar(10) DEFAULT NULL,
  `total_litros` varchar(10) DEFAULT NULL,
  `status_cierre` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_cierre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla combustible_db.table_es_cierre: ~2 rows (aproximadamente)
INSERT IGNORE INTO `table_es_cierre` (`id_cierre`, `id_user`, `fecha_cierre`, `hora_cierre`, `total_efectivob`, `total_efectivod`, `total_tarjeta`, `tasa_dia`, `total_litros`, `status_cierre`) VALUES
	(1, 2, '06-09-25', NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(2, 2, '12-09-25', NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(3, 2, '14-09-25', NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- Volcando estructura para tabla combustible_db.table_es_estacion
CREATE TABLE IF NOT EXISTS `table_es_estacion` (
  `id_estacion` int(11) NOT NULL AUTO_INCREMENT,
  `estacion` varchar(50) DEFAULT NULL,
  `status_estacion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_estacion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla combustible_db.table_es_estacion: ~2 rows (aproximadamente)
INSERT IGNORE INTO `table_es_estacion` (`id_estacion`, `estacion`, `status_estacion`) VALUES
	(1, 'E/S Tachira', 1),
	(2, 'E/S La Gran Parada', 1);

-- Volcando estructura para tabla combustible_db.table_es_tasa_dia
CREATE TABLE IF NOT EXISTS `table_es_tasa_dia` (
  `id_tasa_dia` int(11) NOT NULL AUTO_INCREMENT,
  `tasa_dia` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_tasa_dia`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_es_tasa_dia: ~0 rows (aproximadamente)
INSERT IGNORE INTO `table_es_tasa_dia` (`id_tasa_dia`, `tasa_dia`) VALUES
	(2, '150');

-- Volcando estructura para tabla combustible_db.table_es_tipos_pago
CREATE TABLE IF NOT EXISTS `table_es_tipos_pago` (
  `id_tipo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `status_tipo_pago` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla combustible_db.table_es_tipos_pago: ~5 rows (aproximadamente)
INSERT IGNORE INTO `table_es_tipos_pago` (`id_tipo_pago`, `nombre`, `descripcion`, `status_tipo_pago`) VALUES
	(1, 'Efectivo Divisa', 'Pago en billetes/monedas', 1),
	(2, 'Efectivo Bolivares', 'Pago en Billetes/monedas', 1),
	(3, 'Tarjeta Debito', 'Pago con tarjeta de débito', 1),
	(4, 'Efectivo Euro', 'Pago en Billetes/monedas', 0),
	(5, 'Tarjeta Crédito', 'Pago con tarjeta de crédito', 0);

-- Volcando estructura para tabla combustible_db.table_es_tipos_vehiculo
CREATE TABLE IF NOT EXISTS `table_es_tipos_vehiculo` (
  `id_tipo_vehiculo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `status_tipo_vehiculo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_vehiculo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla combustible_db.table_es_tipos_vehiculo: ~4 rows (aproximadamente)
INSERT IGNORE INTO `table_es_tipos_vehiculo` (`id_tipo_vehiculo`, `nombre`, `descripcion`, `status_tipo_vehiculo`) VALUES
	(1, 'Carro', 'Vehículo particular de pasajeros', 1),
	(2, 'Moto', 'Vehículo de dos ruedas', 1),
	(3, 'Camion', 'Vehículo pesado de carga', 1),
	(4, 'Autobus', 'Vehículo de transporte público', 0);

-- Volcando estructura para tabla combustible_db.table_es_venta
CREATE TABLE IF NOT EXISTS `table_es_venta` (
  `id_venta` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_tipo_pago` int(11) DEFAULT NULL,
  `id_tipo_vehiculo` int(11) DEFAULT NULL,
  `litros` varchar(10) DEFAULT NULL,
  `monto` varchar(10) DEFAULT NULL,
  `id_cierre_diario` int(11) DEFAULT NULL,
  `fecha_venta` varchar(50) DEFAULT NULL,
  `hora_venta` varchar(10) DEFAULT NULL,
  `tasa_dia` varchar(10) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `status_ticket` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla combustible_db.table_es_venta: ~21 rows (aproximadamente)
INSERT IGNORE INTO `table_es_venta` (`id_venta`, `id_user`, `id_tipo_pago`, `id_tipo_vehiculo`, `litros`, `monto`, `id_cierre_diario`, `fecha_venta`, `hora_venta`, `tasa_dia`, `id_rol`, `status_ticket`) VALUES
	(4, 2, 2, 2, '10', '750', 3, '14-09-25', '11:47:59', '150', 1, 0),
	(7, 2, 2, 2, '10', '750', 3, '14-09-25', '11:49:37', '150', 1, 0),
	(9, 2, 1, 3, '50', '25', 3, '14-09-25', '11:50:44', '150', 1, 0),
	(10, 2, 2, 2, '20', '1500', 3, '14-09-25', '12:20:27', '150', 1, 0),
	(11, 2, 2, 2, '20', '1500', 3, '14-09-25', '12:20:55', '150', 1, 0),
	(12, 2, 1, 2, '12', '6', 3, '14-09-25', '12:23:54', '150', 1, 0),
	(13, 2, 1, 1, '50', '25', 3, '14-09-25', '12:24:45', '150', 1, 0),
	(14, 2, 1, 1, '10', '5', 3, '14-09-25', '12:26:27', '150', 1, 0),
	(15, 2, 1, 1, '50', '25', 3, '14-09-25', '12:27:25', '150', 1, 0),
	(16, 2, 2, 2, '10', '750', 3, '14-09-25', '12:28:32', '150', 1, 0),
	(17, 2, 1, 3, '100', '50', 3, '14-09-25', '12:34:58', '150', 1, 0),
	(18, 2, 1, 2, '100', '50', 3, '14-09-25', '12:35:10', '150', 1, 0),
	(19, 2, 1, 1, '10', '5', 3, '14-09-25', '12:44:30', '150', 1, 0),
	(20, 2, 1, 1, '10', '5', 3, '14-09-25', '12:44:39', '150', 1, 0),
	(21, 2, 1, 1, '20', '10', 3, '14-09-25', '12:50:35', '150', 1, 0),
	(22, 2, 1, 1, '20', '10', 3, '14-09-25', '12:52:07', '150', 1, 0),
	(23, 2, 1, 3, '50', '25', 3, '14-09-25', '12:52:42', '150', 1, 0),
	(18, 2, 1, 2, '20', '10', NULL, '14-09-25', '21:47:13', '150', 1, 1);

-- Volcando estructura para tabla combustible_db.table_menu
CREATE TABLE IF NOT EXISTS `table_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_nombre` varchar(50) NOT NULL,
  `menu_icono` varchar(50) NOT NULL,
  `menu_tiene_submenu` tinyint(1) DEFAULT 0,
  `menu_pagina` varchar(100) DEFAULT NULL,
  `menu_orden` int(11) DEFAULT 0,
  `menu_status` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_menu: ~6 rows (aproximadamente)
INSERT IGNORE INTO `table_menu` (`menu_id`, `menu_nombre`, `menu_icono`, `menu_tiene_submenu`, `menu_pagina`, `menu_orden`, `menu_status`, `fecha_creacion`) VALUES
	(1, 'Inicio', 'fas fa-home', 0, 'home', 1, 1, '2025-09-01 20:29:57'),
	(2, 'Usuarios', 'fas fa-users', 1, NULL, 2, 1, '2025-09-01 20:29:57'),
	(3, 'Mi Perfil', 'fas fa-user', 0, 'user/perfil', 5, 1, '2025-09-01 20:29:57'),
	(4, 'Menu', 'fas fa-list', 0, 'menu', 4, 1, '2025-09-01 20:29:57'),
	(5, 'Ayuda', 'fas fa-question-circle', 0, 'ayuda', 8, 0, '2025-09-01 20:29:57'),
	(6, 'Combustible', 'fas fa-cog', 1, NULL, 3, 1, '2025-09-14 01:49:34');

-- Volcando estructura para tabla combustible_db.table_menu_submenu
CREATE TABLE IF NOT EXISTS `table_menu_submenu` (
  `menu_submenu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `submenu_id` int(11) NOT NULL,
  PRIMARY KEY (`menu_submenu_id`),
  KEY `menu_id` (`menu_id`),
  KEY `submenu_id` (`submenu_id`),
  CONSTRAINT `table_menu_submenu_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `table_menu` (`menu_id`),
  CONSTRAINT `table_menu_submenu_ibfk_2` FOREIGN KEY (`submenu_id`) REFERENCES `table_submenu` (`submenu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_menu_submenu: ~1 rows (aproximadamente)
INSERT IGNORE INTO `table_menu_submenu` (`menu_submenu_id`, `menu_id`, `submenu_id`) VALUES
	(1, 2, 1),
	(2, 6, 2);

-- Volcando estructura para tabla combustible_db.table_permisos_rol_menu
CREATE TABLE IF NOT EXISTS `table_permisos_rol_menu` (
  `permiso_id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `submenu_id` int(11) DEFAULT NULL,
  `permiso_status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`permiso_id`),
  KEY `rol_id` (`rol_id`),
  KEY `menu_id` (`menu_id`),
  KEY `submenu_id` (`submenu_id`),
  CONSTRAINT `table_permisos_rol_menu_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `table_roles` (`rol_id`),
  CONSTRAINT `table_permisos_rol_menu_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `table_menu` (`menu_id`),
  CONSTRAINT `table_permisos_rol_menu_ibfk_3` FOREIGN KEY (`submenu_id`) REFERENCES `table_submenu` (`submenu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_permisos_rol_menu: ~8 rows (aproximadamente)
INSERT IGNORE INTO `table_permisos_rol_menu` (`permiso_id`, `rol_id`, `menu_id`, `submenu_id`, `permiso_status`) VALUES
	(20, 2, 6, 2, 1),
	(22, 1, 1, NULL, 1),
	(23, 1, 2, NULL, 1),
	(24, 1, 3, NULL, 1),
	(25, 1, 6, NULL, 1),
	(26, 1, 4, NULL, 1),
	(27, 1, 2, 1, 1),
	(28, 1, 6, 2, 1);

-- Volcando estructura para tabla combustible_db.table_roles
CREATE TABLE IF NOT EXISTS `table_roles` (
  `rol_id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_nombre` varchar(50) NOT NULL,
  `rol_descripcion` text DEFAULT NULL,
  `rol_status` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_roles: ~2 rows (aproximadamente)
INSERT IGNORE INTO `table_roles` (`rol_id`, `rol_nombre`, `rol_descripcion`, `rol_status`, `fecha_creacion`) VALUES
	(1, 'Administrador', 'Acceso completo a todas las funciones del sistema', 1, '2025-09-01 20:41:58'),
	(2, 'Vendedor', 'Acceso a módulos de ventas y clientes', 1, '2025-09-01 20:41:58');

-- Volcando estructura para tabla combustible_db.table_submenu
CREATE TABLE IF NOT EXISTS `table_submenu` (
  `submenu_id` int(11) NOT NULL AUTO_INCREMENT,
  `submenu_nombre` varchar(50) NOT NULL,
  `submenu_pagina` varchar(100) NOT NULL,
  `submenu_url` varchar(100) NOT NULL,
  `submenu_orden` int(11) DEFAULT 0,
  `submenu_status` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`submenu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_submenu: ~2 rows (aproximadamente)
INSERT IGNORE INTO `table_submenu` (`submenu_id`, `submenu_nombre`, `submenu_pagina`, `submenu_url`, `submenu_orden`, `submenu_status`, `fecha_creacion`) VALUES
	(1, 'Agregar Uasuario', 'agregar-usuarios', 'user/newuser', 1, 1, '2025-09-01 20:30:12'),
	(2, 'Registrar venta', 'registrar-venta', 'estacion/registrar', 1, 1, '2025-09-14 02:53:42');

-- Volcando estructura para tabla combustible_db.table_usuarios
CREATE TABLE IF NOT EXISTS `table_usuarios` (
  `usuario_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_nick` varchar(50) NOT NULL,
  `usuario_password` varchar(255) NOT NULL,
  `usuario_nombres` varchar(100) NOT NULL,
  `usuario_apellidos` varchar(100) NOT NULL,
  `usuario_email` varchar(150) NOT NULL,
  `usuario_telefono` varchar(20) DEFAULT NULL,
  `usuario_ci` varchar(15) DEFAULT NULL,
  `usuario_rol_id` int(11) NOT NULL,
  `usuario_departamento_id` int(11) NOT NULL,
  `usuario_direccion` text DEFAULT NULL,
  `usuario_imagen` varchar(255) DEFAULT NULL,
  `usuario_status` tinyint(1) DEFAULT 1,
  `usuario_ruta` varchar(50) DEFAULT NULL,
  `id_estacion` int(11) DEFAULT NULL,
  `usuario_creado` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `usuario_nick` (`usuario_nick`),
  KEY `usuario_rol_id` (`usuario_rol_id`),
  KEY `usuario_departamento_id` (`usuario_departamento_id`),
  CONSTRAINT `table_usuarios_ibfk_1` FOREIGN KEY (`usuario_rol_id`) REFERENCES `table_roles` (`rol_id`),
  CONSTRAINT `table_usuarios_ibfk_2` FOREIGN KEY (`usuario_departamento_id`) REFERENCES `table_departamentos` (`departamento_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_usuarios: ~2 rows (aproximadamente)
INSERT IGNORE INTO `table_usuarios` (`usuario_id`, `usuario_nick`, `usuario_password`, `usuario_nombres`, `usuario_apellidos`, `usuario_email`, `usuario_telefono`, `usuario_ci`, `usuario_rol_id`, `usuario_departamento_id`, `usuario_direccion`, `usuario_imagen`, `usuario_status`, `usuario_ruta`, `id_estacion`, `usuario_creado`) VALUES
	(1, 'admin', 'OCs4Z1hFT083MklFOU15V1NpMS9jdz09', 'Administrador', 'Sistema', 'admin@sistema.com', '123456789', '1250000', 1, 1, 'null', 'storage/ADMIN/default.png', 1, 'system/ADMIN/', 1, '2025-09-01 01:04:19'),
	(2, 'will', 'OCs4Z1hFT083MklFOU15V1NpMS9jdz09', 'Enriquee', 'infante', 'we21@gmail.com', '4125181629', '14607920', 1, 1, 'vista alegre calle principal', 'storage/will/profile_68c21066778806.09923555.jpg', 1, 'system/WILL/', 1, '2025-09-03 18:57:50');

-- Volcando estructura para tabla combustible_db.table_usuario_sessions
CREATE TABLE IF NOT EXISTS `table_usuario_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `usuario_agent` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `last_activity` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_session_id` (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Volcando datos para la tabla combustible_db.table_usuario_sessions: ~1 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
