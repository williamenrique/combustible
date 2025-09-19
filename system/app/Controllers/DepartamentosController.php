<?php
header('Access-Control-Allow-Origin: *');
class Departamentos extends Controllers{
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

    public function departamentos(){
        // Validar nuevamente la sesión antes de mostrar el home
        if (!$this->validateSession()) {
            header("Location:".base_url().'login');
            exit();
        }
        $data = [
            'page_tag' => "Departamentos",
            'page_title' => "Gestión de Departamentos",
            'page_name' => "departamentos",
            'page_link' => "departamentos",
            'page_functions' => "function.departamentos.js"
        ];
        $this->views->getViews($this, "departamentos", $data);
    }

    public function getDepartamentos() {
        $arrData = $this->model->selectDepartamentos();
        for ($i=0; $i < count($arrData); $i++) {
            $status = $arrData[$i]['departamento_status'] == 1 
                ? '<span class="badge badge-success">Activo</span>' 
                : '<span class="badge badge-danger">Inactivo</span>';
            $arrData[$i]['departamento_status'] = $status;

            $btnEdit = '<button class="px-2 py-1 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600" onClick="fntEditDepto('.$arrData[$i]['departamento_id'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="px-2 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600" onClick="fntDelDepto('.$arrData[$i]['departamento_id'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
            $arrData[$i]['acciones'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDelete . '</div>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getDepartamento(int $iddepto) {
        $iddepto = intval($iddepto);
        if ($iddepto > 0) {
            $arrData = $this->model->selectDepartamento($iddepto);
            if (empty($arrData)) {
                $arrResponse = ['success' => false, 'message' => 'Datos no encontrados.'];
            } else {
                $arrResponse = ['success' => true, 'data' => $arrData];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setDepartamento() {
        if ($_POST) {
            $idDepto = intval($_POST['idDepartamento']);
            $nombre = strClean($_POST['txtNombre']);
            $descripcion = strClean($_POST['txtDescripcion']);
            $status = intval($_POST['listStatus']);

            if ($idDepto == 0) {
                // Crear
                $request_depto = $this->model->insertDepartamento($nombre, $descripcion, $status);
                $option = 1;
            } else {
                // Actualizar
                $request_depto = $this->model->updateDepartamento($idDepto, $nombre, $descripcion, $status);
                $option = 2;
            }

            if (intval($request_depto) > 0) {
                if ($option == 1) {
                    $arrResponse = ['success' => true, 'message' => 'Departamento guardado correctamente.'];
                } else {
                    $arrResponse = ['success' => true, 'message' => 'Departamento actualizado correctamente.'];
                }
            } else if ($request_depto == 'exist') {
                $arrResponse = ['success' => false, 'message' => '¡Atención! El departamento ya existe.'];
            } else {
                $arrResponse = ['success' => false, 'message' => 'No es posible almacenar los datos.'];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delDepartamento() {
        if ($_POST) {
            $idDepto = intval($_POST['idDepartamento']);
            $requestDelete = $this->model->deleteDepartamento($idDepto);
            if ($requestDelete == 'in_use') {
                $arrResponse = ['success' => false, 'message' => 'No se puede eliminar. El departamento está asignado a uno o más usuarios.'];
            } else if ($requestDelete) {
                $arrResponse = ['success' => true, 'message' => 'Se ha eliminado el departamento.'];
            } else {
                $arrResponse = ['success' => false, 'message' => 'Error al eliminar el departamento.'];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}