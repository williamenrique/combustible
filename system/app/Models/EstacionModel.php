<?php
class EstacionModel extends Mysql {
    public function __construct(){
        parent::__construct();
    }
    /* * iniial data
    */
    public function selectTipoVehiculo(){
        $sql = "SELECT id_tipo_vehiculo, nombre FROM table_es_tipos_vehiculo WHERE status_tipo_vehiculo = 1";
        $request = $this->select_all($sql);
        return $request;
    }
    public function updateTasa(float $tasa){
        $this->tasa = $tasa;
        $sql = "UPDATE table_es_tasa_dia SET tasa_dia = ?";
        $arrData = array($this->tasa);
        $request = $this->update($sql,$arrData);
        return $request;
    }
    public function getTasa(){
        $sql = "SELECT tasa_dia FROM table_es_tasa_dia LIMIT 1";
        $request = $this->select($sql);
        return $request;
    }
    public function selectTipoPago(){
        $sql = "SELECT id_tipo_pago, nombre FROM table_es_tipos_pago WHERE status_tipo_pago = 1";
        $request = $this->select_all($sql);
        return $request;
    }
    public function getLastTicket(int $intIdUser, string $srtDate){
        $sql = "SELECT tVenta.*, tVehiculo.nombre AS tipoVehiculo FROM table_es_venta tVenta
                INNER JOIN table_es_tipos_vehiculo tVehiculo ON tVenta.id_tipo_vehiculo = tVehiculo.id_tipo_vehiculo
                WHERE tVenta.fecha_venta = ?  AND tVenta.id_user = ? AND tVenta.status_ticket = 1
                ORDER BY tVenta.id_venta DESC LIMIT 10";
        $request = $this->select_all($sql, [$srtDate, $intIdUser]);
        return $request;
    }
    public function getDetail(int $intIdUser, string $srtDate) {
        $sql = "SELECT
                    COUNT(v.id_venta) AS total_ventas,
                    COALESCE(SUM(CAST(v.litros AS DECIMAL(10,2))), 0) AS total_litros,
                    COALESCE(SUM(CASE WHEN v.id_tipo_pago = 1 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0) AS total_divisa,
                    COALESCE(SUM(CASE WHEN v.id_tipo_pago = 2 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0) AS total_efectivo,
                    COALESCE(SUM(CASE WHEN v.id_tipo_pago = 3 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0) AS total_debito,
                    COALESCE(SUM(CASE WHEN v.id_tipo_pago = 1 THEN CAST(v.monto AS DECIMAL(10,2)) * (SELECT tasa_dia FROM table_es_venta WHERE fecha_venta = ? AND id_user = ? ORDER BY id_venta DESC LIMIT 1) ELSE 0 END), 0) AS `divisa_a_bs`,
                    (COALESCE(SUM(CASE WHEN v.id_tipo_pago = 2 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0) +
                    COALESCE(SUM(CASE WHEN v.id_tipo_pago = 3 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0) +
                    COALESCE(SUM(CASE WHEN v.id_tipo_pago = 1 THEN CAST(v.monto AS DECIMAL(10,2)) * (SELECT tasa_dia FROM table_es_venta WHERE fecha_venta = ? AND id_user = ? ORDER BY id_venta DESC LIMIT 1) ELSE 0 END), 0)) AS `total_bs`
                FROM table_es_venta v
            WHERE v.fecha_venta = ? AND v.status_ticket = 1 AND v.id_user = ?";
        $resumenGeneral = $this->select($sql, [$srtDate, $intIdUser,$srtDate, $intIdUser, $srtDate, $intIdUser]);
        $sqlVehiculos = "SELECT
                            tv.nombre AS tipo_vehiculo,
                            COUNT(v.id_tipo_vehiculo) AS cantidad
                        FROM table_es_venta v
                        INNER JOIN table_es_tipos_vehiculo tv ON v.id_tipo_vehiculo = tv.id_tipo_vehiculo
                        WHERE v.fecha_venta = ? AND v.status_ticket = 1 AND v.id_user = ?
                        GROUP BY v.id_tipo_vehiculo";
        $tiposVehiculo = $this->select_all($sqlVehiculos, [$srtDate, $intIdUser]);
        $resumenGeneral['tiposVehiculo'] = $tiposVehiculo;
        return $resumenGeneral;
    }
    public function getPendingCierres(int $idUser){
        $sql = "SELECT id_cierre, fecha_cierre FROM table_es_cierre WHERE id_user = ? AND status_cierre = 0";
        $request = $this->select_all($sql, [$idUser]);
        return $request;
    }
    public function getPendingVentas(int $idUser) {
        $sql = "SELECT DISTINCT fecha_venta, id_user
                FROM table_es_venta
                WHERE id_user = ? AND fecha_venta != ? AND (id_cierre_diario IS NULL OR id_cierre_diario = 0)
                ORDER BY fecha_venta DESC";
        $fechaActual = date("d-m-y");
        $request = $this->select_all($sql, [$idUser, $fechaActual]);
        return $request;
    }
    /* * end initial data
    */
    public function setVenta(int $useId, int $tipoVehiculo, float $litros, int $tipoPago, float $monto, float $tasa) {
        $fecha = date("d-m-y");
        $hora = date("H:i:s");
        $idRol = 1;
        $statusTicket = 1;
        $idCierreDiario = 0;
        $countQuery = "SELECT COUNT(*) FROM table_es_venta WHERE fecha_venta = ?";
        $requestCount = $this->contar($countQuery, [$fecha]);
        $numeroTicket = $requestCount + 1;
        $sql_insert = "INSERT INTO table_es_venta(id_venta, id_user, id_tipo_pago, id_tipo_vehiculo, litros, monto, id_cierre_diario,fecha_venta, hora_venta, tasa_dia, id_rol, status_ticket) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
        $request_insert = $this->insert($sql_insert,[$numeroTicket, $useId, $tipoPago, $tipoVehiculo, $litros, $monto,$idCierreDiario, $fecha, $hora, $tasa, $idRol, $statusTicket]);
        return $numeroTicket;
    }
    // obtener la data despues de registrar venta o imprimir un ticket
    public function getTicketData(int $intIdVenta,int $intUser,string $srtFecha){
        $sql = "SELECT tVenta.*, tu.* ,tv.nombre AS tipoVehiculo, tp.nombre AS tipoPago, estacion.estacion  
                FROM table_es_venta tVenta 
                        INNER JOIN table_usuarios tu ON tVenta.id_user = tu.usuario_id 
                        INNER JOIN table_es_tipos_pago tp ON tVenta.id_tipo_pago = tp.id_tipo_pago
                        INNER JOIN table_es_tipos_vehiculo tv ON tVenta.id_tipo_vehiculo = tv.id_tipo_vehiculo
                        INNER JOIN table_es_estacion estacion ON estacion.id_estacion = tu.id_estacion
                WHERE tVenta.id_venta = ? AND tVenta.fecha_venta = ? AND tVenta.id_user = ?";
        $request = $this->select($sql, [$intIdVenta, $srtFecha,$intUser]);
        return $request;
    }
    // cerrar dia o alguno pendiente
    public function setDailyCierre(int $idUser, string $fechaCierre){
        $sql = "INSERT INTO table_es_cierre(id_user, fecha_cierre, status_cierre) VALUES (?, ?, 1)";
        $request = $this->insert($sql, [$idUser, $fechaCierre]);
        if($request > 0){
            $sql_update = "UPDATE table_es_venta SET id_cierre_diario = ?, status_ticket = ?  WHERE id_user = ? AND fecha_venta = ? AND status_ticket = 1";
            $this->update($sql_update, [$request,0, $idUser, $fechaCierre]);
        }
        return $request;
    }
    // obtener data despues de cerrar dia o alguno pendiente
    public function getDataCierre(int $intIdUser, string $srtDate) {
        $sql = "SELECT 
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 1 THEN 1 END) AS Automovil,
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 2 THEN 1 END) AS Motocicleta,
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 3 THEN 1 END) AS Camion,
                tVenta.fecha_venta AS fecha,
                COUNT(*) AS total_ventas,
                tVenta.tasa_dia AS tasa_dia,
                tUser.usuario_nombres AS operador,
                tEstacion.estacion,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 1 THEN litros ELSE 0 END) AS litrosAuto,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 2 THEN litros ELSE 0 END) AS litrosMoto,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 3 THEN litros ELSE 0 END) AS litrosCamion,
                SUM(monto) AS total_general,
                SUM(litros) AS total_litros,
                SUM(CASE WHEN id_tipo_pago = 1 THEN monto ELSE 0 END) AS total_divisa,
                SUM(CASE WHEN id_tipo_pago = 2 THEN monto ELSE 0 END) AS total_efectivo,
                SUM(CASE WHEN id_tipo_pago = 3 THEN monto ELSE 0 END) AS total_debito,
                SUM(CASE WHEN id_tipo_pago = 1 THEN monto * CAST(tVenta.tasa_dia AS DECIMAL(10,2)) ELSE 0 END) AS total_divisa_efectivo,
                SUM(CASE 
                    WHEN id_tipo_pago = 1 THEN monto * CAST(tVenta.tasa_dia AS DECIMAL(10,2))
                    ELSE monto 
                END) AS total_general_bs
            FROM table_es_venta tVenta
            JOIN table_es_tipos_vehiculo tVehiculo ON tVenta.id_tipo_vehiculo = tVehiculo.id_tipo_vehiculo
            JOIN table_usuarios tUser ON tventa.id_user = tUser.usuario_id
            LEFT JOIN table_es_estacion tEstacion ON tEstacion.id_estacion = tUser.id_estacion
            WHERE tVenta.id_user = ? AND tVenta.fecha_venta = ?
            GROUP BY tVenta.fecha_venta, tVenta.tasa_dia, tUser.usuario_nombres, tEstacion.estacion";
        return  $this->select($sql, [ $intIdUser,$srtDate]);  
    }
    // obtener data para imprimir detallado de ventas
    public function getDetallado(int $intIdUser, string $srtDate){
        $this->intIdUser = $intIdUser;
        $this->srtDate = $srtDate;
        $sql = "SELECT 
                    tVenta.id_venta AS numero_venta,
                    tVenta.fecha_venta AS fecha_venta,
                    tVehiculo.nombre AS tipo_vehiculo,
                    tVenta.litros AS cantidad_litros,
                    tVenta.monto,
                    tVenta.tasa_dia,
                    tPago.nombre AS tipo_pago,
                    tUser.usuario_nombres AS operador,
                    tEstacion.estacion
                FROM 
                    table_es_venta tVenta
                JOIN 
                    table_es_tipos_vehiculo tVehiculo ON tVenta.id_tipo_vehiculo = tVehiculo.id_tipo_vehiculo
                JOIN 
                    table_es_tipos_pago tPago ON tVenta.id_tipo_pago = tPago.id_tipo_pago
                JOIN 
                    table_usuarios tUser ON tVenta.id_user = tUser.usuario_id
                LEFT JOIN 
                    table_es_estacion tEstacion ON tEstacion.id_estacion = tUser.id_estacion
                WHERE 
                    tVenta.fecha_venta = '$this->srtDate'
                    AND tVenta.id_user = $this->intIdUser
                ORDER BY 
                    tVenta.fecha_venta ASC";
        return $this->select_all($sql);
    }
    // metodo para obtener datos para pdf
    public function getDataVenta(string $srtDate, int $intIdUser){
        $fechaFormateada = DateTime::createFromFormat('d-m-y', $srtDate)->format('Y-m-d');
        $this->srtDate = $fechaFormateada;
        $this->intIdUser = $intIdUser;
        $sql = "SELECT 
            tventa.id_venta AS numero_venta,
            tventa.fecha_venta,
            tvehiculo.nombre AS tipo_vehiculo,
            tventa.litros AS cantidad_litros,
            tventa.monto,
            tventa.id_cierre_diario,
            tventa.id_user,
            tventa.tasa_dia,
            CASE 
                WHEN tpago.id_tipo_pago = 1 THEN tventa.monto * tventa.tasa_dia
                WHEN tpago.id_tipo_pago = 2 THEN tventa.monto
                ELSE NULL 
            END AS efectivob,
            CASE WHEN tpago.id_tipo_pago = 3 THEN tventa.monto ELSE NULL END AS tarjeta_debito,
            tUser.usuario_nombres AS empleado
        FROM 
            table_es_venta tventa
        JOIN 
            table_es_tipos_vehiculo tvehiculo ON tventa.id_tipo_vehiculo = tvehiculo.id_tipo_vehiculo
        JOIN 
            table_es_tipos_pago tpago ON tventa.id_tipo_pago = tpago.id_tipo_pago
        JOIN 
            table_usuarios tUser ON tventa.id_user = tUser.usuario_id
        WHERE 
            tventa.fecha_venta = ? 
            AND tventa.id_user = ?
        ORDER BY 
        tventa.fecha_venta";
        $request = $this->select_all($sql, [$srtDate,$intIdUser]);
        return $request;
    }
    // metodo para total del pdf
    public function getTotal(string $srtDate, int $intIdUser){
        $fechaFormateada = DateTime::createFromFormat('d-m-y', $srtDate)->format('Y-m-d');
        $this->srtDate = $fechaFormateada;
        $this->intIdUser = $intIdUser;
        $sql = "SELECT
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 1 THEN 0 END) AS Automovil,
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 2 THEN 0 END) AS Motocicleta,
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 3 THEN 0 END) AS Camion,
                tVenta.fecha_venta AS fecha,
                COUNT(*) AS total_ventas,
                tVenta.tasa_dia AS tasa_dia,
                tUser.usuario_nombres AS empleado,
                tEstacion.estacion,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 1 THEN litros ELSE 0 END) AS litrosAuto,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 2 THEN litros ELSE 0 END) AS litrosMoto,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 3 THEN litros ELSE 0 END) AS litrosCamion,
                SUM(monto) AS total_general,
                SUM(litros) AS total_litros,
                SUM(CASE WHEN id_tipo_pago = 1 THEN monto ELSE 0 END) AS total_divisa,
                SUM(CASE WHEN id_tipo_pago = 3 THEN monto ELSE 0 END) AS total_debito,
                SUM(
                    CASE
                    WHEN id_tipo_pago = 1 THEN monto * tVenta.tasa_dia
                    WHEN id_tipo_pago = 2 THEN monto
                    ELSE 0
                    END
                ) AS total_efectivo_bs
                FROM table_es_venta tVenta
                JOIN table_es_tipos_vehiculo tVehiculo ON tVenta.id_tipo_vehiculo = tVehiculo.id_tipo_vehiculo
                JOIN table_usuarios tUser ON tventa.id_user = tUser.usuario_id
                LEFT JOIN table_es_estacion tEstacion ON tEstacion.id_estacion = tUser.id_estacion
                WHERE tVenta.fecha_venta = ? 
                AND tventa.id_user = ?
                GROUP BY tVenta.fecha_venta";
        $request = $this->select($sql, [$srtDate,$intIdUser]);
        return $request;
    }
    
    public function getLitrosTotalesSistema() {
        $sql = "SELECT COALESCE(SUM(CAST(litros AS DECIMAL(10,2))), 0) AS total_litros FROM table_es_venta";
        $request = $this->select($sql);
        return $request['total_litros'] ?? 0;
    }
    // En EstacionModel.php, mejorar la función existente:
    public function getLitrosPorFecha(string $srtDate) {
        $sql = "SELECT COALESCE(SUM(CAST(litros AS DECIMAL(10,2))), 0) AS total_litros 
                FROM table_es_venta 
                WHERE fecha_venta = ?";
        $request = $this->select($sql, [$srtDate]);
        return $request['total_litros'] ?? 0;
    }
    public function getHistorialCierres() {
        $sql = "SELECT 
                c.id_cierre,
                c.fecha_cierre,
                c.id_user,
                u.usuario_nombres,
                u.usuario_apellidos,
                v.tasa_dia,
                (COALESCE(SUM(CASE WHEN v.id_tipo_pago = 1 THEN CAST(v.monto AS DECIMAL(10,2)) * CAST(c.tasa_dia AS DECIMAL(10,2)) ELSE 0 END), 0) +
                COALESCE(SUM(CASE WHEN v.id_tipo_pago = 2 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0)) AS `efectivo_bs`,
                COALESCE(SUM(CASE WHEN v.id_tipo_pago = 3 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0) AS `debito_bs`,
                ((COALESCE(SUM(CASE WHEN v.id_tipo_pago = 1 THEN CAST(v.monto AS DECIMAL(10,2)) * CAST(c.tasa_dia AS DECIMAL(10,2)) ELSE 0 END), 0) +
                COALESCE(SUM(CASE WHEN v.id_tipo_pago = 2 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0)) +
                COALESCE(SUM(CASE WHEN v.id_tipo_pago = 3 THEN CAST(v.monto AS DECIMAL(10,2)) ELSE 0 END), 0)) AS `total_bs`,
                COALESCE(SUM(CAST(v.litros AS DECIMAL(10,2))), 0) AS `total_litros_vendidos`
                FROM table_es_cierre c
                INNER JOIN table_usuarios u ON c.id_user = u.usuario_id
                INNER JOIN table_es_venta v ON c.id_cierre = v.id_cierre_diario
                GROUP BY c.fecha_cierre, c.id_user, u.usuario_apellidos, u.usuario_nombres, c.tasa_dia
                ORDER BY c.fecha_cierre DESC, c.id_user";
        $request = $this->select_all($sql);
        return $request;
    }
    public function getVentasByCierre($idCierre,$idUser,$fechaCierre) {
        $sql = "SELECT 
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 1 THEN   0 END) AS Automovil,
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 2 THEN   0 END) AS Motocicleta,
                COUNT(CASE WHEN tVenta.id_tipo_vehiculo = 3 THEN  0 END) AS Camion,
                tVenta.fecha_venta AS fecha,
                COUNT(*) AS total_ventas,
                tVenta.tasa_dia AS tasa_dia,
                tUser.usuario_nombres AS empleado,
                tEstacion.estacion,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 1 THEN litros ELSE 0 END) AS litrosAuto,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 2 THEN litros ELSE 0 END) AS litrosMoto,
                SUM(CASE WHEN tVenta.id_tipo_vehiculo = 3 THEN litros ELSE 0 END) AS litrosCamion,
                SUM(monto) AS total_general,
                SUM(litros) AS total_litros,
                SUM(CASE WHEN id_tipo_pago = 1 THEN monto ELSE 0 END) AS total_divisa,
                SUM(CASE WHEN id_tipo_pago = 2 THEN monto ELSE 0 END) AS total_efectivo,
                SUM(CASE WHEN id_tipo_pago = 3 THEN monto ELSE 0 END) AS total_debito
                FROM table_es_venta tVenta
                JOIN table_es_tipos_vehiculo tVehiculo ON tVenta.id_tipo_vehiculo = tVehiculo.id_tipo_vehiculo
                JOIN table_usuarios tUser ON tventa.id_user = tUser.usuario_id
                LEFT JOIN table_es_estacion tEstacion ON tEstacion.id_estacion = tUser.id_estacion
                WHERE tVenta.fecha_venta = ?
                AND tventa.id_user =  ?
                GROUP BY tVenta.fecha_venta";
        return  $this->select_all($sql, [$fechaCierre,$idUser]);
    }
    public function getDatosParaReporte($idCierre) {
        $sqlCierre = "
            SELECT 
                c.id_cierre,
                c.fecha_cierre,
                c.total_litros,
                c.total_general,
                c.total_efectivo,
                c.total_debito,
                c.tasa_dia,
                u.usuario_nombres,
                u.usuario_apellidos
            FROM table_es_cierre c
            INNER JOIN table_usuarios u ON c.id_user = u.usuario_id
            WHERE c.id_cierre = ?
        ";
        $arrDataCierre = array($idCierre);
        $resumen = $this->select($sqlCierre, $arrDataCierre);
        if (empty($resumen)) {
            return null;
        }
        $sqlDetalles = "
            SELECT
                v.id_venta,
                v.fecha_venta,
                v.litros,
                v.monto,
                v.monto_efectivo,
                v.monto_debito,
                v.hora_venta,
                tv.nombre AS tipo_vehiculo,
                tp.nombre AS tipo_pago
            FROM table_es_venta v
            INNER JOIN table_es_tipos_vehiculo tv ON v.id_tipo_vehiculo = tv.id_tipo_vehiculo
            INNER JOIN table_es_tipos_pago tp ON v.id_tipo_pago = tp.id_tipo_pago
            WHERE v.id_cierre_diario = ?
            ORDER BY v.id_venta ASC
        ";
        $arrDataDetalles = array($idCierre);
        $detalles = $this->select_all($sqlDetalles, $arrDataDetalles);
        return ['resumen' => $resumen, 'detalles' => $detalles];
    }
    public function deleteVenta($idVenta,$srtFecha,$idUSer,) {
        $sql = "DELETE FROM table_es_venta WHERE id_venta = ? AND fecha_venta = ? AND id_user = ?";
        return $this->delete($sql, [$idVenta,$srtFecha,$idUSer]);
    }

    // mostrar datos para lista
    // Agregar esta función en EstacionModel.php
    public function getFechasConVentas() {
        $sql = "SELECT DISTINCT fecha_venta 
                FROM table_es_venta 
                ORDER BY STR_TO_DATE(fecha_venta, '%d-%m-%y') DESC";
        return $this->select_all($sql);
    }
}