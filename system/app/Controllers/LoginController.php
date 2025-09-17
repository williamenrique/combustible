<?php
header('Access-Control-Allow-Origin: *');
class Login extends Controllers{
    private $db;
	public function __construct(){
		// Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
		//invocar para que se ejecute el metodo de la herencia
		parent::__construct();

	}
	public function login(){
		//invocar la vista con views y usamos getViews y pasamos parametros esta clase y la vista
		//incluimos un arreglo que contendra toda la informacion que se enviara al home
		$data['page_tag'] = ESTACION . " - LOGIN";
		$data['page_title'] = "Login";
		$data['page_name'] = "login";
		$data['page_functions'] = "function.login.js";
        $data['db_error'] = null; // Inicializamos la variable de error
        try {
            // Intentamos instanciar el modelo. Esto activa la conexión
            $this->model = new LoginModel();
            // Verificar si hay error de conexión en el modelo
            if ($this->model->hasConnectionError()) {
                $data['db_error'] = "Error de conexión: " . $this->model->getConnectionError();
            }
        } catch (PDOException $e) {
            // Si hay un error, lo guardamos en una variable para la vista
            $data['db_error'] = "Fallo de conexión: " . $this->getFriendlyErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $data['db_error'] = "Error inesperado: " . $e->getMessage();
        }
        // La vista se carga después de que se ha verificado el estado de la conexión
        $this->views->getViews($this, "login", $data);
    }
    // Método para traducir mensajes de error técnicos a mensajes amigables
    private function getFriendlyErrorMessage($errorMessage) {
        if (strpos($errorMessage, 'Unknown database') !== false) {
            return "La base de datos no fue encontrada. Contacte al administrador.";
        } elseif (strpos($errorMessage, 'Access denied') !== false) {
            return "Credenciales de acceso incorrectas para la base de datos.";
        } elseif (strpos($errorMessage, 'Connection refused') !== false) {
            return "No se puede conectar al servidor de base de datos. Verifique que el servidor esté ejecutándose.";
        } else {
            return "Error de conexión con la base de datos: " . $errorMessage;
        }
    }
    
	public function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Método no permitido');
            return;
        }
        try {
             // Verificar conexión primero
            $strUser = filter_var(strtolower($_POST['txtUser'] ?? ''), FILTER_SANITIZE_STRING);
            $strPass = $_POST['txtPass'] ?? '';
            if (empty($strUser) || empty($strPass)) {
                $this->jsonResponse(false, 'Error en datos');
                return;
            }
            $encryptedPass = encryption($strPass);
            $requestUser = $this->model->loginUser($strUser, $encryptedPass);
            if (empty($requestUser)) {
                $this->jsonResponse(false, 'El usuario o el password es incorrecto');
                return;
            }
            if ($requestUser['usuario_status'] != 1) {
                $this->jsonResponse(false, 'El usuario está inactivo');
                return;
            }
            $this->initUserSession($requestUser['usuario_id']);
            $this->jsonResponse(true, 'ok');

        } catch (Exception $e) {
            $this->jsonResponse(false, 'Error en el proceso'. $e);
        }
    }
	public function initUserSession($userId) {
        // Verificar si el usuario ya tiene una sesión activa
        $activeSession = $this->model->getActiveSession($userId,NULL);
        if ($activeSession) {
            // Si ya existe una sesión activa, eliminar la anterior
            $this->jsonResponse(false, 'Ya hay una sesión abierta para este usuario', 'session_exists');
            // $this->model->deleteSession($activeSession['session_id']);
            exit;
        }
        // Crear nueva sesión
        session_regenerate_id(true);
        $_SESSION['idUser'] = $userId;
        $_SESSION['login'] = true;
        $_SESSION['session_id'] = session_id();
        $_SESSION['session_created'] = time();
        $_SESSION['last_activity'] = time();
        $userData = $this->model->sessionLogin($userId);
        // Guardar información de la sesión en la base de datos
        $this->model->saveSessionInfo([
            'session_id' => session_id(),
            'usuario_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'usuario_agent' => $_SERVER['HTTP_USER_AGENT'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
	/*********
	 * crear usuario desde el registro nuevo
	 */
	public function createUser(){
		if($_POST){
			$registerName = ucwords($_POST['registerName']);
			$registerCi = intval($_POST['registerCi']);
			$registerEmail = strtolower($_POST['registerEmail']);
			$registerPassword = $_POST['registerPassword'];
			$registerRepeatPassword = $_POST['registerRepeatPassword'];
			if(empty($_POST['registerRepeatPassword'])) {
				$arrResponse = array("status" => false, "msg" => "Clave no puede estar vacia");
			}else if($_POST['registerPassword'] == $_POST['registerRepeatPassword']){
				$strPassEncript = encryption($registerRepeatPassword);
				$requestUser = $this->model->createUser($registerCi,$registerName,$registerEmail,$strPassEncript);
				// echo $requestUser;
				if($requestUser > 0){
					//comprobar si el nick ya esta en uso
					$userNIck =  substr($registerName,0,1).'UN'.'-0'.$requestUser;
					$fileBase = "./storage/". $userNIck . "/";
					$fileHash = substr(md5($fileBase . uniqid(microtime() . mt_rand())), 0, 8);
					// creo carpeta en servidor si no existe
					if (!file_exists($fileBase))
					mkdir($fileBase, 0777, true);
					$sqlUpdate = $this->model->updateNick($requestUser,$registerCi,$registerEmail,$userNIck,$fileBase);
					$sqlUserRol = $this->model->setUserRol($requestUser,3);
					$arrResponse = array("status" => true, "msg" => "Cuenta creada");
				}else if($requestUser == "exist"){
					$arrResponse = array("status" => false, "msg" => "Atencion! email o identificacion ya existe ingrese otro");
				}else{
					$arrResponse = array("status" => false, "msg" => "No es posible crear la cuenta");
				}
			}else{
				$arrResponse = array("status" => false, "msg" => "Claves no coinciden");
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}
    // forzar la session activa para cerrarla si ya existe 
    public function forceLogout() {
        // Recibe el userId por POST (puede venir como JSON o FormData)
        $userNick = $_POST['userId'] ?? null;
        if (!$userNick) {
            // Si no viene por POST, intenta obtenerlo por JSON
            $data = json_decode(file_get_contents('php://input'), true);
            $userNick = $data['userId'] ?? null;
        }
        if ($userNick) {
            $activeSession = $this->model->getActiveSession(NULL, $userNick);
            if ($activeSession) {
                $this->model->deleteSession($activeSession['session_id']);
                $this->jsonResponse(true, 'Sesión anterior cerrada. Ahora puedes iniciar sesión.');
            } else {
                $this->jsonResponse(false, 'No se encontró sesión activa para cerrar.');
            }
        } else {
            $this->jsonResponse(false, 'No se recibió el usuario.');
        }
    }
	public function jsonResponse($status, $msg, $code = null) {
        $response = [
            'status' => $status,
            'msg' => $msg
        ];
        if ($code) {
            $response['code'] = $code;
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
}
	// end class
}