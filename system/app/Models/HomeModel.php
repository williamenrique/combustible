<?php
class HomeModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}

	public function getAvailableMonths() {
		$sql = "SELECT DISTINCT DATE_FORMAT(STR_TO_DATE(fecha_venta, '%d-%m-%y'), '%Y-%m') AS mes
				FROM table_es_venta
				ORDER BY mes ASC";
		// Solo retorna los datos, no imprime nada
		return $this->select_all($sql);
	}

	public function getMonthlyLiters($startMonth, $endMonth) {
		// Convertir los meses YYYY-MM a fechas válidas para la comparación
		$startDate = date('d-m-y', strtotime($startMonth . '-01'));
		$endDate = date('t-m-y', strtotime($endMonth . '-01')); // 't' da el último día del mes
		$sql = "SELECT
					DATE_FORMAT(STR_TO_DATE(v.fecha_venta, '%d-%m-%y'), '%Y-%m') AS mes_venta,
					COALESCE(SUM(CAST(v.litros AS DECIMAL(10,2))), 0) AS total_litros
				FROM table_es_venta v
				WHERE
					STR_TO_DATE(v.fecha_venta, '%d-%m-%y') BETWEEN STR_TO_DATE(?, '%d-%m-%y') AND STR_TO_DATE(?, '%d-%m-%y')
				GROUP BY
					mes_venta
				ORDER BY
					mes_venta ASC";
		return $this->select_all($sql, [$startDate, $endDate]);
	}
	// funciones para mostrar resumen por usuario en grafica de barras
	public function getDailySalesByUser($fecha = null) {
		if ($fecha === null) {
			$fecha = date('d-m-y'); // Fecha actual en formato dd-mm-yy
		}
		$sql = "SELECT 
					u.usuario_nick as usuario,
					COUNT(v.id_venta) as total_ventas,
					COALESCE(SUM(CAST(v.litros AS DECIMAL(10,2))), 0) as total_litros,
					COALESCE(SUM(CAST(v.monto AS DECIMAL(10,2))), 0) as total_monto
				FROM table_usuarios u
				LEFT JOIN table_es_venta v ON u.usuario_id = v.id_user 
					AND v.fecha_venta = ?
				WHERE u.usuario_status = 1 
				GROUP BY u.usuario_id, u.usuario_nick
				ORDER BY total_litros DESC";
		
		return $this->select_all($sql, [$fecha]);
	}
	public function getDailySalesSummary($fecha = null) {
		if ($fecha === null) {
			$fecha = date('d-m-y'); // Fecha actual en formato dd-mm-yy
		}
		$sql = "SELECT 
					COUNT(v.id_venta) as total_ventas,
					COALESCE(SUM(CAST(v.litros AS DECIMAL(10,2))), 0) as total_litros,
					COALESCE(SUM(CAST(v.monto AS DECIMAL(10,2))), 0) as total_monto,
					COUNT(DISTINCT v.id_user) as total_usuarios
				FROM table_es_venta v
				WHERE v.fecha_venta = ?";
		
		return $this->select($sql, [$fecha]);
	}
}