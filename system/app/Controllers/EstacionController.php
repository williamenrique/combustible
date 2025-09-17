<?php
header('Access-Control-Allow-Origin: *');
class Estacion extends Controllers{
    private $db; //para inicializar la base de datos
    public function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Validar sesión de manera más robusta
        if (!$this->validateSession()) {
            header("Location:".base_url().'login');
            exit();
        }
        //invocar para que se ejecute el metodo de la herencia
        parent::__construct();
		$this->estacionModel = new EstacionModel();

    }
    /*manejo de sesiones activas*/
	function getActiveSession(){
		$reuest = $this->model->getActiveSession($_SESSION['idUser']);
	}
    public function validateSession() {
        // Verificar si la sesión está iniciada y es válida
        if (empty($_SESSION['login']) || empty($_SESSION['idUser'])) {
            return false;
        }
        if (isset($_SESSION['session_id'])) {
            $validSession = validateSessionDB($_SESSION['session_id'], $_SESSION['idUser']);
            if (!$validSession) {
                deleteSession($_SESSION['session_id']);
                return false;
            }
        }
        return true;
    }
    /*fin manejo de sesiones activas*/
    /**inicio de manejo de errores en cada controlador debe estar */
	private function handleDatabaseError($error) {
        // Log del error
        error_log("Error de BD en controlador User: " . $error);
        // Puedes elegir cómo manejar el error:
        // 1. Redirigir a una página de error
        // 2. Mostrar un mensaje JSON (para APIs)
        // 3. Guardar en variable para mostrar en vista
        // Para métodos que devuelven JSON:
        if ($this->isAja|xRequest()) {
            $arrResponse = [
                'success' => false,
                'message' => 'Error de conexión a la base de datos',
                'error' => $error
            ];
            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        } else {
            // Para vistas HTML, podrías guardar el error para mostrarlo
            $_SESSION['error_message'] = "Error de base de datos: " . $error;
        }
    }
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    /******************
     * 
     * INICIA LA VISTA
     */
	/**fin de manejo de errores en cada controlador debe estar*/
    public function registrar(){
        // Validar nuevamente la sesión antes de mostrar el home
        if (!$this->validateSession()) {
            header("Location:".base_url().'login');
            exit();
        }
        $data = [
            'page_tag' => "Pagina principal",
            'page_title' => "Pagina Principal",
            'page_name' => "combustible",
            'page_link' => "registrar-venta",
            'page_functions' => "function.estacion.js"
        ];
        $this->views->getViews($this, "registrar", $data);
    }

	public function initialData() {
        $arrResponse = array('success' => false, 'message' => '');
        try {
            $tiposVehiculo = $this->estacionModel->selectTipoVehiculo();
            $tiposPago = $this->estacionModel->selectTipoPago();
            $tasa = $this->estacionModel->getTasa();
            $ultimosTickets = $this->estacionModel->getLastTicket($_SESSION['idUser'], date('d-m-y'));
            $resumen = $this->estacionModel->getDetail($_SESSION['idUser'], date('d-m-y'));
            $cierresPendientes = $this->estacionModel->getPendingCierres($_SESSION['idUser']);
			// OBTENER VENTAS PENDIENTES EN VEZ DE CIERRES
            $ventasPendientes = $this->estacionModel->getPendingVentas($_SESSION['idUser']);
            $arrResponse = [
				'success' => true,
                'message' => 'Datos cargados correctamente',
				'ventasPendientes' => $ventasPendientes,
                'tiposVehiculo' => $tiposVehiculo,
                'tiposPago' => $tiposPago,
                'tasa' => $tasa,
                'ultimosTickets' => $ultimosTickets,
                'resumen' => $resumen,
                'cierresPendientes' => $cierresPendientes
            ];
        } catch (Exception $e) {
            $arrResponse['message'] = 'Error al cargar datos iniciales: ' . $e->getMessage();
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function updateTasa() {
        $arrResponse = array('success' => false, 'message' => '');
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['tasa'])) {
            $arrResponse['message'] = 'Tasa no especificada.';
        } else {
            $tasa = floatval($data['tasa']);
            $request = $this->estacionModel->updateTasa($tasa);
            if ($request) {
                $arrResponse = ['success' => true, 'message' => 'Tasa actualizada correctamente.'];
            } else {
                $arrResponse['message'] = 'Error al actualizar la tasa.';
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrarVenta() {
        $arrResponse = array('success' => false, 'message' => '');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $arrResponse['message'] = 'Método no permitido.';
            echo json_encode($arrResponse);
            return;
        }
        try {
            // Validaciones básicas de los datos
            if (empty($_POST['txtLTS']) || empty($_POST['txtListTipoVehiculo']) || empty($_POST['txtListTipoPago'])) {
                throw new Exception('Datos de venta incompletos.');
            }
            $idUser = $_SESSION['idUser'];
            $tipoVehiculo = intval($_POST['txtListTipoVehiculo']);
            $litros = floatval($_POST['txtLTS']);
            $tipoPago = intval($_POST['txtListTipoPago']);
            $monto = floatval($_POST['txtMonto']);
            $tasa = floatval($_POST['txtTasa']);
            $request = $this->estacionModel->setVenta($idUser, $tipoVehiculo, $litros, $tipoPago, $monto, $tasa);
            if ($request > 0) {
				$datTicket = $this->model->getTicketData($request,$_SESSION['userData']['usuario_id'],date('d-m-y'));
				// dep($datTicket);
                $arrResponse = ['success' => true, 'message' => 'Venta registrada con éxito. Ticket #' . $request,'ticketData'=>$datTicket];
            } else {
                $arrResponse['message'] = 'Error al registrar la venta.';
            }
        } catch (Exception $e) {
            $arrResponse['message'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function cerrarDia() {
		$arrResponse = array('success' => false, 'message' => '');
		try {
			// Leer el cuerpo de la solicitud JSON
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);
			// Verificar que los datos y la fecha existan
			if (!isset($data['fecha_cierre'])) {
				throw new Exception("Error: La fecha de cierre no fue recibida.");
			}
			$fechaCierre = $data['fecha_cierre'];
			$userId = $data['userId'];
			// Asumiendo que el modelo ya tiene la lógica para cerrar el turno pendiente
			$request = $this->model->setDailyCierre($userId, $fechaCierre);
			if ($request) {
				$resumen_vacio = [
					'total_ventas' => 0,
					'total_litros' => 0.00,
					'total_bs' => 0.00,
					'total_divisa' => 0.00,
					'total_efectivo' => 0.00,
					'total_debito' => 0.00,
					'tiposVehiculo' => [],
					'tiposPago' => [],
				];
				$getDtaCierre = $this->model->getDataCierre($userId, $fechaCierre);
				$arrResponse = ['success' => true, 'message' => 'Día cerrado exitosamente.','dataCierre' => $getDtaCierre, 'resumen' => $resumen_vacio];
			} else {
				$arrResponse['message'] = 'Error al cerrar el día. Puede que ya esté cerrado o no haya ventas.';
			}
		} catch (Exception $e) {
			$arrResponse['message'] = 'Error: ' . $e->getMessage();
		}
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
    public function cerrarTurnoPendiente() {
		$arrResponse = array('success' => false, 'message' => '');
		try {
			// Leer el cuerpo de la solicitud JSON
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);
			// Verificar que los datos y la fecha existan
			if (!isset($data['fecha_cierre'])) {
				throw new Exception("Error: La fecha de cierre no fue recibida.");
			}
			$fechaCierre = $data['fecha_cierre'];
			$userId = $data['userId'];
			// Asumiendo que el modelo ya tiene la lógica para cerrar el turno pendiente
			$request = $this->model->setDailyCierre($userId, $fechaCierre);
			if ($request) {
				$resumen_vacio = [
					'total_ventas' => 0,
					'total_litros' => 0.00,
					'total_bs' => 0.00,
					'total_divisa' => 0.00,
					'total_efectivo' => 0.00,
					'total_debito' => 0.00,
					'tiposVehiculo' => [],
					'tiposPago' => [],
				];
				$getDtaCierre = $this->model->getDataCierre($userId, $fechaCierre);
				$arrResponse = ['success' => true, 'message' => 'Cierre de día pendiente realizado con éxito.','dataCierre' => $getDtaCierre, 'resumen' => $resumen_vacio];

			} else {
				$arrResponse['message'] = 'Error al cerrar el turno pendiente.';
			}
		} catch (Exception $e) {
			$arrResponse['message'] = 'Error: ' . $e->getMessage();
		}
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	// obtener data para imrimir detallado del dia
	public function getDetalleVentas() {
        $arrResponse = array('success' => false, 'message' => '');
        try {
			// Leer el cuerpo de la solicitud JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
			// Verificar que los datos existan
			if (!isset($data['fecha_detalle'])) {
				throw new Exception("Error: Datos incompletos.");
			}
            $fechaTicket = $data['fecha_detalle'];
            $request = $this->model->getDetallado($_SESSION['userData']['usuario_id'], $fechaTicket);
            if ($request > 0) {
                $arrResponse = ['success' => true, 'message' => 'Ticket obtenido', 'ticketData' => $request];
            } else {
                $arrResponse['message'] = 'Error al imprimir ticket.';
            }
        } catch (Exception $e) {
            $arrResponse['message'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    // obtener data para imprimir un ticket
	public function getTicket() {
        $arrResponse = array('success' => false, 'message' => '');
        try {
			 // Leer el cuerpo de la solicitud JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
			// Verificar que los datos existan
			if (!isset($data['idVenta']) || !isset($data['fechaTicket'])) {
				throw new Exception("Error: Datos incompletos.");
			}
            $idVenta = $data['idVenta'];
            $fechaTicket = $data['fechaTicket'];
            $idUser = $data['idUser'];
            $request = $this->model->getTicketData($idVenta, $idUser, $fechaTicket);
            if ($request > 0) {
                $arrResponse = ['success' => true, 'message' => 'Ticket obtenido', 'ticketData' => $request];
            } else {
                $arrResponse['message'] = 'Error al imprimir ticket.';
            }
        } catch (Exception $e) {
            $arrResponse['message'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
	// para generar el pdf
	public function generarReportePdf() {
		header('Content-Type: application/json');
		$arrResponse = array('success' => false, 'message' => '');
		try {
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);
			if (!isset($data['fecha'])) { // Se elimina la validación de idUser
				throw new Exception("Error: Datos incompletos.");
			}
			$fecha = $data['fecha'];
			$idUser = $data['idUser']; // Usa el id de la sesión
			// $idUser = $_SESSION['userData']['usuario_id']; // Usa el id de la sesión
			$dataTotal = $this->model->getTotal($fecha, $idUser);
			$dataDetallado = $this->model->getDataVenta($fecha, $idUser);
			// dep($dataTotal);
			if (empty($dataTotal) || empty($dataDetallado)) {
				$arrResponse = ['success' => false, 'message' => 'No se encontraron datos de ventas para esta fecha y usuario.'];
			} else {
				$arrResponse = ['success' => true, 'message' => 'Datos obtenidos para el reporte.', 'data' => ['dataTotal' => $dataTotal, 'dataDetallado' => $dataDetallado]];
			}
		} catch (Exception $e) {
			$arrResponse['message'] = 'Error: ' . $e->getMessage();
		}
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	/* 
    * inicio vista dataventa
    **/
    public function dataVenta(){
        // Validar nuevamente la sesión antes de mostrar el home
        if (!$this->validateSession()) {
            header("Location:".base_url().'login');
            exit();
        }
        $data = [
            'page_tag' => "Pagina principal",
            'page_title' => "Pagina Principal",
            'page_name' => "combustible",
            'page_link' => "mantenimiento",
            'page_functions' => "function.dataventa.js"
        ];
        $this->views->getViews($this, "dataVenta", $data);
    }
    /*
    * inicio del init
    * TODO: Nuevos métodos para la sección de historial de cierres y ventas
    */
    // mostrar litros totales en tarjeta
	public function getLitrosTotales() {
        $arrResponse = array('success' => false, 'message' => '');
        try {
            $totalLitros = $this->estacionModel->getLitrosTotalesSistema();
            $arrResponse = ['success' => true, 'totalLitros' => $totalLitros];
        } catch (Exception $e) {
            $arrResponse['message'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    // tabla historial de cierres
    public function getHistorialCierres() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            try {
                $arrData = $this->estacionModel->getHistorialCierres();
                if (empty($arrData)) {
                    $arrResponse = ['success' => false, 'message' => 'No se encontraron cierres realizados.'];
                } else {
                    $arrResponse = ['success' => true, 'data' => $arrData];
                }
            } catch (Exception $e) {
                $arrResponse = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    // Trae las ventas de un cierre específico.
    public function getVentasByCierre() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $postData = json_decode(file_get_contents('php://input'), true);
                if (!isset($postData['idCierre']) || empty($postData['idCierre'])) {
                    $arrResponse = ['success' => false, 'message' => 'ID del cierre no especificado.'];
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }
                $idCierre = intval($postData['idCierre']);
                $idUser = intval($postData['iduser']);
                $fechaCierre = $postData['fechaCierre'];
                $arrData = $this->estacionModel->getDataVenta($fechaCierre,$idUser);
                if (empty($arrData)) {
                    $arrResponse = ['success' => false, 'message' => 'No se encontraron ventas para este cierre.'];
                } else {
                    $arrResponse = ['success' => true, 'data' => $arrData];
                }
            } catch (Exception $e) {
                $arrResponse = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    //Elimina una venta específica.
    public function deleteVenta() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $postData = json_decode(file_get_contents('php://input'), true);
                if (!isset($postData['idVenta']) || empty($postData['idVenta'])) {
                    $arrResponse = ['success' => false, 'message' => 'ID de venta no especificado.'];
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                $idVenta = intval($postData['idVenta']);
                $idUser = intval($postData['idUser']);
                $fechaTicket = $postData['fechaTicket'];
                $deleted = $this->estacionModel->deleteVenta($idVenta,$fechaTicket,$idUser); // Se asume que esta función existe en el modelo.
                
                if ($deleted) {
                    $arrResponse = ['success' => true, 'message' => 'Venta eliminada correctamente.'];
                } else {
                    $arrResponse = ['success' => false, 'message' => 'Error al eliminar la venta o no se encontró.'];
                }
            } catch (Exception $e) {
                $arrResponse = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    // obtener datos del cierre
    public function getDataCierre() {
        $arrResponse = array('success' => false, 'message' => '');
        try {
			 // Leer el cuerpo de la solicitud JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
			// Verificar que los datos existan
			if (!isset($data['idUser']) || !isset($data['fecha_venta'])) {
				throw new Exception("Error: Datos incompletos.");
			}
            $idUser = $data['idUser'];
            $fechaVenta = $data['fecha_venta'];
            $request = $this->model->getDataCierre($idUser, $fechaVenta);
            if ($request > 0) {
                $arrResponse = ['success' => true, 'message' => 'Cierre obtenido', 'cierreData' => $request];
            } else {
                $arrResponse['message'] = 'Error al obtener cierre.';
            }
        } catch (Exception $e) {
            $arrResponse['message'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
	




    /**
     * Trae los datos de un cierre específico para ser impresos sin necesidad de cerrar el día.
     */
    public function getDatosParaReporte() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $postData = json_decode(file_get_contents('php://input'), true);
                if (!isset($postData['idCierre']) || empty($postData['idCierre'])) {
                    $arrResponse = ['success' => false, 'message' => 'ID de cierre no especificado.'];
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }

                $idCierre = intval($postData['idCierre']);
                $dataCierre = $this->estacionModel->getDatosParaReporte($idCierre);
                
                if (!empty($dataCierre)) {
                    $arrResponse = ['success' => true, 'dataCierre' => $dataCierre];
                } else {
                    $arrResponse = ['success' => false, 'message' => 'No se encontraron datos para el reporte de este cierre.'];
                }
            } catch (Exception $e) {
                $arrResponse = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    // Agregar esta función en EstacionController.php
    public function getFechasConVentas() {
        $arrResponse = array('success' => false, 'message' => '');
        try {
            $fechas = $this->estacionModel->getFechasConVentas();
            $arrResponse = ['success' => true, 'fechas' => $fechas];
        } catch (Exception $e) {
            $arrResponse['message'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    // traer data de litros por fecha
    public function getLitrosPorFecha() {
        $arrResponse = array('success' => false, 'message' => '');
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            if (!isset($data['fecha'])) {
                throw new Exception("Error: Dato 'fecha' no proporcionado.");
            }
            $totalLitros = $this->estacionModel->getLitrosPorFecha($data['fecha']);
            $arrResponse = ['success' => true, 'totalLitros' => $totalLitros];
            
        } catch (Exception $e) {
            $arrResponse['message'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}