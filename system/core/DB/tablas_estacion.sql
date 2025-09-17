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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


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

-- Volcando datos para la tabla combustible_db.table_es_tasa_dia: ~1 rows (aproximadamente)
INSERT IGNORE INTO `table_es_tasa_dia` (`id_tasa_dia`, `tasa_dia`) VALUES
	(2, '135');

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

-- Volcando datos para la tabla combustible_db.table_es_venta: ~3 rows (aproximadamente)
INSERT IGNORE INTO `table_es_venta` (`id_venta`, `id_user`, `id_tipo_pago`, `id_tipo_vehiculo`, `litros`, `monto`, `id_cierre_diario`, `fecha_venta`, `hora_venta`, `tasa_dia`, `id_rol`, `status_ticket`) VALUES
	(1, 13, 2, 1, '20', '1350', NULL, '05-09-25', '', '135', 1, 1),
	(2, 5, 2, 2, '10', '675', NULL, '05-09-25', '', '135', 1, 1),
	(3, 5, 1, 2, '20', '10', NULL, '05-09-25', '', '135', 1, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
