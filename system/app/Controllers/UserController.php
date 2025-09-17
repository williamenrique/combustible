<?php
header('Access-Control-Allow-Origin: *');
class User extends Controllers{
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
		// Inicializar la conexión a BD
    	$this->db = new Mysql();

    }
	// Luego crea un método helper:
	private function checkDBConnection() {
		if ($this->db->hasError()) {
			throw new Exception('Error de conexión a la base de datos: ' . $this->db->getError());
		}
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
	/**fin de manejo de errores en cada controlador debe estar*/
	//TODO: inicio de vista
	public function perfil(){
		// Validar nuevamente la sesión antes de mostrar el home
        if (!$this->validateSession()) {
            header("Location:".base_url().'login');
            exit();
        }
		//invocar la vista con views y usamos getView y pasamos parametros esta clase y la vista
		//incluimos un arreglo que contendra toda la informacion que se enviara al home
		$data = [
			'page_tag' => "Pagina principal",
			'page_title' => "Pagina Principal",
			'page_name' => "user/perfil",
			'page_link' => "active-home",//activar el menu desplegable o un lin solo
			'page_functions' => "function.user.js"
		];
		$this->views->getViews($this, "perfil", $data);
	}
	// cambiar imagen de usuario
	public function subirImagen(){
		$arrResponse = array('success' => false, 'message' => '', 'ruta_imagen' => '');
		try {
			// Verificar que se haya enviado una imagen
			if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
				throw new Exception('No se ha seleccionado ninguna imagen o ocurrió un error en la carga');
			}
			// Obtener datos del usuario desde el formulario
			$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
			if ($id_usuario === 0) {
				throw new Exception('ID de usuario no válido');
			}
			// Configuración
			$archivos_permitidos = array('jpg', 'jpeg', 'png', 'gif', 'svg', 'webp');
			$max_size = 8 * 1024 * 1024; // 8MB en bytes
			$max_width = 800;
			$max_height = 800;
			// Obtener información del archivo
			$file = $_FILES['imagen'];
			$fileData = pathinfo($file['name']);
			$fileExtension = strtolower($fileData['extension']);
			// Validaciones
			if (!in_array($fileExtension, $archivos_permitidos)) {
				throw new Exception('Formato de archivo no permitido. Use: ' . implode(', ', $archivos_permitidos));
			}
			if ($file['size'] > $max_size) {
				throw new Exception('La imagen es demasiado grande. Tamaño máximo: 8MB');
			}
			// Crear directorio si no existe
			$baseDirectory = 'storage/'.$_SESSION['userData']['usuario_nick'].'/';
			if (!file_exists($baseDirectory)) {
				if (!mkdir($baseDirectory, 0777, true)) {
					throw new Exception('No se pudo crear el directorio de almacenamiento');
				}
			}
			// Generar nombre único para el archivo
			$fileName = uniqid('profile_', true) . '.' . $fileExtension;
			$filePath = $baseDirectory . $fileName;
			// Validar y procesar imagen
			$imageInfo = getimagesize($file['tmp_name']);
			if (!$imageInfo) {
				throw new Exception('El archivo no es una imagen válida');
			}
			// Opcional: Redimensionar imagen si es muy grande
			list($width, $height) = $imageInfo;
			if ($width > $max_width || $height > $max_height) {
				// Aquí podrías agregar lógica de redimensionamiento
				// usando GD library o Intervention Image
			}
			// Mover archivo al directorio destino
			if (!move_uploaded_file($file['tmp_name'], $filePath)) {
				throw new Exception('Error al guardar la imagen en el servidor');
			}
			// Eliminar imagen anterior si existe
			if (!empty($_SESSION['userData']['usuario_imagen']) && file_exists($_SESSION['userData']['usuario_imagen'])) {
				@unlink($_SESSION['userData']['usuario_imagen']);
			}
			// Actualizar en base de datos
			$requestUser = $this->model->updateImg($id_usuario, $filePath);
			if (!$requestUser) {
				// Si falla la BD, eliminar la imagen subida
				@unlink($filePath);
				throw new Exception('Error al actualizar la base de datos');
			}
			// Actualizar sesión
			sessionUser($_SESSION['idUser']);
	
			$arrResponse = [
				'success' => true,
				'message' => 'Imagen de perfil actualizada correctamente',
				'ruta_imagen' => $filePath,
				'userData' => $_SESSION['userData'] // Opcional: enviar datos actualizados
			];
	
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage(),
				'ruta_imagen' => ''
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	// funcion cambiar password
	public function cambiarPassword() {
		$arrResponse = array('success' => false, 'message' => '');	
		try {
			// uso de manejo de error Verificar conexión primero
            if ($this->model->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->model->getError());
			}
			// Obtener datos JSON
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);
			if (!$data || !isset($data['currentPassword']) || !isset($data['newPassword']) || !isset($data['id_usuario'])) {
				throw new Exception('Datos incompletos');
			}
			$currentPassword = $data['currentPassword'];
			$newPassword = $data['newPassword'];
			$id_usuario = intval($data['id_usuario']);
			// Validaciones
			if (empty($currentPassword) || empty($newPassword)) {
				throw new Exception('Las contraseñas no pueden estar vacías');
			}
			if (strlen($newPassword) < 2) {
				throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
			}
			// Verificar usuario y obtener datos
			// echo $id_usuario;
			$userData = $this->model->selectUsuario($id_usuario);
			if (empty($userData)) {
				throw new Exception('Usuario no encontrado');
			}
			// Verificar contraseña actual
			// if (!password_verify($currentPassword, $userData['usuario_password'])) {
			// 	throw new Exception('La contraseña actual es incorrecta');
			// }
			if ($currentPassword != decryption($userData['usuario_password'])) {
				// echo "nueva ".$currentPassword.' actual '. decryption($userData['usuario_password']);
				throw new Exception('La contraseña actual es incorrecta');
			}
			// Hash de la nueva contraseña
			// $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
			$hashedPassword = encryption($newPassword);
			// Actualizar en base de datos
			$requestUpdate = $this->model->updatePassword($id_usuario, $hashedPassword);
			if (!$requestUpdate) {
				throw new Exception('Error al actualizar la contraseña en la base de datos');
			}
			$arrResponse = [
				'success' => true,
				'message' => 'Contraseña actualizada correctamente'
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
	
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	// funcion actualizar datos del usuario
	public function actualizarDatos() {
		$arrResponse = array('success' => false, 'message' => '');
		try {
			// uso de manejo de error Verificar conexión primero
            if ($this->model->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->model->getError());
			}
			// colocarlo siempre para manejo de errores
			// Obtener datos JSON
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);
	
			if (!$data || !isset($data['id_usuario'])) {
				throw new Exception('Datos incompletos');
			}
			$usuario_id = intval($data['id_usuario']);
			$nombres = trim($data['usuario_nombres'] ?? '');
			$apellidos = trim($data['usuario_apellidos'] ?? '');
			$email = trim($data['usuario_email'] ?? '');
			$telefono = trim($data['usuario_telefono'] ?? '');
			$direccion = trim($data['usuario_direccion'] ?? '');
			// Validaciones
			if (empty($nombres)) {
				throw new Exception('El nombre es obligatorio');
			}
			if (empty($apellidos)) {
				throw new Exception('El apellido es obligatorio');
			}
			if (empty($email)) {
				throw new Exception('El correo electrónico es obligatorio');
			}
			// Validar formato de email
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				throw new Exception('El formato del correo electrónico no es válido');
			}
			// Verificar si el email ya existe para otro usuario
			$existingUser = $this->model->checkEmailExists($email, $usuario_id);
			// 
			if ($existingUser) {
				throw new Exception('El correo electrónico ya está en uso por otro usuario');
			}
			// Actualizar en base de datos
			$requestUpdate = $this->model->updateUserData($usuario_id, [
				'usuario_nombres' => $nombres,
				'usuario_apellidos' => $apellidos,
				'usuario_email' => $email,
				'usuario_telefono' => $telefono,
				'usuario_direccion' => $direccion
			]);
			// dep($requestUpdate);
			// se puede usar esta o no 
			// if (!$requestUpdate) {
			// 	throw new Exception('Error al actualizar los datos en la base de datos');
			// }
			 // VERIFICAR ERROR INMEDIATAMENTE después de la actualización
			// if (!$requestUpdate['success']) {
			// 	throw new Exception('Error al actualizar: ' . $requestUpdate['error']);
			// }
			// Obtener datos actualizados
			$userData = $this->model->selectUsuario($usuario_id);
			// Actualizar sesión
			$_SESSION['userData'] = array_merge($_SESSION['userData'], [
				'usuario_nombres' => $nombres,
				'usuario_apellidos' => $apellidos,
				'usuario_email' => $email,
				'usuario_telefono' => $telefono,
				'usuario_direccion' => $direccion
			]);
			$arrResponse = [
				'success' => true,
				'message' => 'Datos actualizados correctamente',
				'userData' => $userData
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	// TODO: incio de ista
	public function newuser(){
		//invocar la vista con views y usamos getView y pasamos parametros esta clase y la vista
		//incluimos un arreglo que contendra toda la informacion que se enviara al home
		$data = [
			'page_tag' => "Pagina principal",
			'page_title' => "Pagina Principal",
			'page_name' => "user/agregar",
			'page_link' => "active-home",//activar el menu desplegable o un lin solo
			'page_functions' => "function.user.js"
		];
		$this->views->getViews($this, "newuser", $data);
	}
	// Obtener roles para select
	public function getRoles() {
		$arrResponse = array('success' => false, 'roles' => array());
		try {
			// uso de manejo de error Verificar conexión primero
            if ($this->model->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->model->getError());
			}
			
			$roles = $this->model->getRoles();
			
			$arrResponse = [
				'success' => true,
				'roles' => $roles
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}

	// Obtener departamentos para select
	public function getDepartments() {
		$arrResponse = array('success' => false, 'departments' => array());
		try {
			if ($this->db->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->db->getAnyError());
			}
			$departments = $this->model->getDepartments();
			
			$arrResponse = [
				'success' => true,
				'departments' => $departments
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
/***** Crear usuario **********/
	public function setUser() {
		$arrResponse = array('success' => false, 'message' => '');
		try {
			if ($this->model->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->model->getError());
			}
			// Obtener datos JSON
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);
			if (!$data) {
				throw new Exception('Datos incompletos');
			}
			$intIdentificacion = intval($data['txtIdPersonal'] ?? 0);
			$strNombre = ucwords(trim($data['txtNombre'] ?? ''));
			$strApellidos = ucwords(trim($data['txtApellido'] ?? ''));
			$srtDireccion = ucwords(trim($data['txtDireccion'] ?? ''));
			$intTlf = intval($data['txtTelefono'] ?? 0);
			$strEmail = strtolower(trim($data['txtEmail'] ?? ''));
			$intlistRolId = intval($data['listRolId'] ?? 0);
			$intlistDep = intval($data['listDep'] ?? 0);
			
			// comienzan las validaciones
			if ($intIdentificacion === 0) {
				throw new Exception('La identificación es obligatoria');
			}
			if (empty($strNombre)) {
				throw new Exception('El nombre es obligatorio');
			}
			if (empty($strApellidos)) {
				throw new Exception('El apellido es obligatorio');
			}
			if (empty($strEmail)) {
				throw new Exception('El correo electrónico es obligatorio');
			}
			if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
				throw new Exception('El formato del correo electrónico no es válido');
			}
			if ($intlistRolId === 0) {
				throw new Exception('Debe seleccionar un rol');
			}
			if ($intlistDep === 0) {
				throw new Exception('Debe seleccionar un departamento');
			}
			// validado que no ingresen vacion o datos erroneos
			// Verificar si el email ya existe
			$existingEmail = $this->model->checkEmailExists($strEmail, 0);
			if ($existingEmail) {
				throw new Exception('El correo electrónico ya está en uso por otro usuario');
			}
			// Verificar si la identificación ya existe
			$existingId = $this->model->checkIdExists($intIdentificacion);
			if ($existingId) {
				throw new Exception('La identificación ya está en uso por otro usuario');
			}		
			// Crear contraseña por defecto (123456)
			$strPass = encryption('123456');
			// Insertar usuario ($identificacion, $nombre, $apellidos, $telefono, $email,$direccion, $password)
			$requestUser = $this->model->insertUser($intIdentificacion, $strNombre,$intlistRolId,$intlistDep, $strApellidos, $intTlf, $strEmail, $srtDireccion, $strPass);
			if ($requestUser == 0) {
				throw new Exception('Error al crear el usuario en la base de datos');
			}
			if ($requestUser == "exist") {
				throw new Exception('Usuario existente');
			}
			// Crear nick y carpeta
			$userNIck = substr($strNombre, 0, 1) . substr($strApellidos, 0, 1) . '-' . $requestUser;
			$fileBase = "storage/" . $userNIck . "/";
			// Crear directorio si no existe
			if (!file_exists($fileBase)) {
				if (!mkdir($fileBase, 0777, true)) {
					throw new Exception('No se pudo crear el directorio de almacenamiento');
				}
			}
			// Actualizar nick en base de datos
			$createNick = $this->model->createNick($requestUser, $intIdentificacion, $strEmail, $userNIck, $intlistRolId, $fileBase, $intlistDep);
			if (!$createNick) {
				throw new Exception('Error al actualizar el nick del usuario');
			}
			// Copiar imagen por defecto
			$source = "src/img/favicon.ico";
			$destination = $fileBase . 'default.png';
			if (file_exists($source)) {
				if (!copy($source, $destination)) {
					print_r("No se pudo copiar la imagen por defecto para el usuario " . $requestUser);
				}
			}
			$arrResponse = [
				'success' => true,
				'message' => 'Usuario creado correctamente',
				'userId' => $requestUser
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
/**** mostrar usuarios en tabla y sus acciones *******/
	// Obtener todos los usuarios para DataTable
	public function getUsuarios() {
		$arrResponse = array('success' => false, 'data' => array());
		try {
			if ($this->model->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->model->getError());
			}
			$usuarios = $this->model->getUsuarios();
			$arrResponse = [
				'success' => true,
				'data' => $usuarios
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	// Obtener un usuario específico para edición
	public function getUsuario($idUsuario) {
		$arrResponse = array('success' => false, 'usuario' => array());
		try {
			if ($this->model->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->model->getError());
			}
			$idUsuario = intval($idUsuario);
			if ($idUsuario <= 0) {
				throw new Exception('ID de usuario no válido');
			}
			$usuario = $this->model->getUsuario($idUsuario);
			if (!$usuario) {
				throw new Exception('Usuario no encontrado');
			}
			$arrResponse = [
				'success' => true,
				'usuario' => $usuario
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
/**** fin mostrar usuarios en tabla y sus acciones *******/

/**** fin mostrar usuarios en tabla y sus acciones *******/
	// Actualizar usuario
	public function updateUsuario() {
		$arrResponse = array('success' => false, 'message' => '');
		try {
			if ($this->model->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->model->getError());
			}
			// Obtener datos JSON
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);
			
			if (!$data || !isset($data['usuario_id'])) {
				throw new Exception('Datos incompletos');
			}
			// Validaciones
			if (empty($data['usuario_nombres'])) {
				throw new Exception('El nombre es obligatorio');
			}
			if (empty($data['usuario_apellidos'])) {
				throw new Exception('El apellido es obligatorio');
			}
			if (empty($data['usuario_email'])) {
				throw new Exception('El correo electrónico es obligatorio');
			}
			if (!filter_var($data['usuario_email'], FILTER_VALIDATE_EMAIL)) {
				throw new Exception('El formato del correo electrónico no es válido');
			}
			// Verificar si el email ya existe para otro usuario
			$existingUser = $this->model->checkEmailExists($data['usuario_email'], $data['usuario_id']);
			if ($existingUser) {
				throw new Exception('El correo electrónico ya está en uso por otro usuario');
			}
			// Actualizar en base de datos
			$requestUpdate = $this->model->updateUsuario($data);
			
			if (!$requestUpdate) {
				throw new Exception('Error al actualizar el usuario en la base de datos');
			}
			$arrResponse = [
				'success' => true,
				'message' => 'Usuario actualizado correctamente'
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	// Actualizar estado del usuario
	public function updateStatus() {
		$arrResponse = array('success' => false, 'message' => '');
		try {
			if ($this->model->hasError()) {
				throw new Exception('Error de conexión a la base de datos: ' . $this->model->getError());
			}
			// Obtener datos JSON
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);
			
			if (!$data || !isset($data['usuario_id']) || !isset($data['usuario_status'])) {
				throw new Exception('Datos incompletos');
			}
			// Actualizar estado en base de datos
			$requestUpdate = $this->model->updateStatus($data['usuario_id'], $data['usuario_status']);
			if (!$requestUpdate) {
				throw new Exception('Error al actualizar el estado del usuario');
			}
			$arrResponse = [
				'success' => true,
				'message' => 'Estado actualizado correctamente'
			];
		} catch (Exception $e) {
			$arrResponse = [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}

	// Vista de lista de usuarios
	public function usuarios() {
		$data = [
			'page_tag' => "Gestión de Usuarios",
			'page_title' => "Lista de Usuarios",
			'page_name' => "user/usuarios",
			'page_link' => "active-users",
			'page_functions' => "function.user.js"
		];
		$this->views->getViews($this, "usuarios", $data);
	}
}