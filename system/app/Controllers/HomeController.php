<?php
header('Access-Control-Allow-Origin: *');
class Home extends Controllers{
    private $db;
    public function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!$this->validateSession()) {
            header("Location:".base_url().'login');
            exit();
        }
        parent::__construct();
    }
    function getActiveSession(){
        $reuest = $this->model->getActiveSession($_SESSION['idUser']);
    }
    public function validateSession() {
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
    private function handleDatabaseError($error) {
        error_log("Error de BD en controlador User: " . $error);
        if ($this->isAjaxRequest()) {
            $arrResponse = [
                'success' => false,
                'message' => 'Error de conexión a la base de datos',
                'error' => $error
            ];
            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        } else {
            $_SESSION['error_message'] = "Error de base de datos: " . $error;
        }
    }
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    public function home(){
        if (!$this->validateSession()) {
            header("Location:".base_url().'login');
            exit();
        }
        $data = [
            'page_tag' => "Pagina principal",
            'page_title' => "Pagina Principal",
            'page_name' => "home",
            'page_link' => "active-home",
            'page_functions' => "function.home.js"
        ];
        $this->views->getViews($this, "home", $data);
    }
    // seleccionar meses para  el select
    public function getAvailableMonths() {
        $data = $this->model->getAvailableMonths();
        if ($data) {
            $response = ['success' => true, 'data' => $data];
        } else {
            $response = ['success' => false, 'message' => 'No hay datos para mostrar.'];
        }
        echo json_encode($response);
    }
    // mostrar dona 
    public function getMonthlyLiters() {
        if (!isset($_POST['start_month']) || !isset($_POST['end_month'])) {
            $response = ['success' => false, 'message' => 'Parámetros incompletos.'];
            echo json_encode($response);
            return;
        }
        
        $startMonth = $_POST['start_month'];
        $endMonth = $_POST['end_month'];
        
        // Validar formato de meses (YYYY-MM)
        if (!preg_match('/^\d{4}-\d{2}$/', $startMonth) || !preg_match('/^\d{4}-\d{2}$/', $endMonth)) {
            $response = ['success' => false, 'message' => 'Formato de fecha inválido.'];
            echo json_encode($response);
            return;
        }
        
        $data = $this->model->getMonthlyLiters($startMonth, $endMonth);
        
        if ($data) {
            $response = ['success' => true, 'data' => $data];
        } else {
            $response = ['success' => false, 'message' => 'No hay datos para mostrar.'];
        }
        
        echo json_encode($response);
    }
    public function getDailySales() {
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('d-m-y');
        
        try {
            // Obtener ventas por usuario
            $salesByUser = $this->model->getDailySalesByUser($fecha);
            
            // Obtener resumen general del día
            $dailySummary = $this->model->getDailySalesSummary($fecha);
            
            $response = [
                'success' => true,
                'data' => [
                    'sales_by_user' => $salesByUser,
                    'daily_summary' => $dailySummary,
                    'fecha' => $fecha
                ]
            ];
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al obtener datos de ventas del día: ' . $e->getMessage()
            ];
        }
        
        echo json_encode($response);
    }
}