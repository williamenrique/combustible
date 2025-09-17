<?php
class LoginModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function loginUser(string $txtUser, string $txtPass){
		$sql = "SELECT * FROM table_usuarios WHERE (usuario_email = ? OR usuario_nick = ?) AND  usuario_password = ? AND usuario_status != 0";
		$request = $this->select($sql, [$txtUser, $txtUser, $txtPass]);
		return $request;
	}

	public function sessionLogin(int $intIdUser){
		$sql = "SELECT usuario.*, rol.*, departamento.departamento_nombre FROM table_usuarios  usuario
				JOIN table_roles rol ON usuario.usuario_rol_id = rol.rol_id 
				JOIN table_departamentos departamento ON usuario.usuario_departamento_id = departamento.departamento_id
				WHERE usuario.usuario_id = ?";
		$request = $this->select($sql, [$intIdUser]);
		$_SESSION['userData'] = $request;
		return $request;
	}

	// verificar sesion abierta
	public function saveSessionInfo($data) {
		$query = "INSERT INTO table_usuario_sessions 
				(session_id, usuario_id, ip_address, usuario_agent, created_at) 
				VALUES (?, ?, ?, ?, ?) 
				ON DUPLICATE KEY UPDATE 
				last_activity = NOW()";
		return $this->insert($query, [
			$data['session_id'], 
			$data['usuario_id'], 
			$data['ip_address'], 
			$data['usuario_agent'], 
			$data['created_at']
		]);
    }
	// probando esta funcion
    public function validateSessionDB($sessionId, $userId) {
    // ELIMINAR el timeout de 30 minutos
		$query = "SELECT * FROM table_usuario_sessions 
				WHERE session_id = ? AND usuario_id = ?";
		return $this->select($query, [$sessionId, $userId]);
	}
	// probando esta funcion
	public function getActiveSession(int $userId = null, string $userNick = null) {
		// Construir la consulta dinámica según los parámetros recibidos
		$where = [];
		$arrData = [];
		if (!empty($userId)) {
			$where[] = 'tsession.usuario_id = ?';
			$arrData[] = $userId;
		}
		if (!empty($userNick)) {
			$where[] = "tuser.usuario_nick = ?";
			$arrData[] = $userNick;
		}
		if (empty($where)) {
			// Si ambos están vacíos, retorna null o false
			return null;
		}
		$whereSql = implode(' OR ', $where);
		// ELIMINAR la condición de 30 minutos
		$query = "SELECT tsession.* , tuser.usuario_nick
				FROM table_usuario_sessions tsession
				JOIN table_usuarios tuser ON tsession.usuario_id = tuser.usuario_id
				WHERE ($whereSql)
				ORDER BY tsession.last_activity DESC
				LIMIT 1";
		return $this->select($query, $arrData);
	}
	// funcion anterior
	public function getActiveSessionB(int $userId = null, string $userNick = null) {
		// Construir la consulta dinámica según los parámetros recibidos
		$where = [];
		$arrData = [];
		if (!empty($userId)) {
			// $where[] = 'tsession.usuario_id = '.$userId;
			$where[] = 'tsession.usuario_id = ?';
			$arrData[] = $userId;
		}
		if (!empty($userNick)) {
			$where[] = "tuser.usuario_nick = ?";
			$arrData[] = $userNick;
		}
		if (empty($where)) {
			// Si ambos están vacíos, retorna null o false
			return null;
		}
		$whereSql = implode(' OR ', $where);

		$query = "SELECT tsession.* , tuser.usuario_nick
				FROM table_usuario_sessions tsession
				JOIN table_usuarios tuser ON tsession.usuario_id = tuser.usuario_id
				WHERE ($whereSql)
				AND tsession.last_activity > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
				ORDER BY tsession.last_activity DESC
				LIMIT 1";
		return $this->select($query, $arrData);
	}
    // funcion anterior
    public function validateSessionDBB($sessionId, $userId) {
        $query = "SELECT * FROM table_usuario_sessions 
                WHERE session_id = ? AND usuario_id = ? 
                AND last_activity > DATE_SUB(NOW(), INTERVAL 30 MINUTE)";
        return $this->select($query, [$sessionId, $userId]);
    }
    
    public function deleteSession($sessionId) {
		$this->sessionId = $sessionId;
        $query = "DELETE FROM table_usuario_sessions WHERE session_id = ?";
        return $this->delete($query, [$sessionId]);
    }
    public function cleanupExpiredSessions() {
		// En lugar de 1 día, puedes poner 30 días o más
		$query = "DELETE FROM table_usuario_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 2 DAY)";
		return $this->delete($query);
	}
	// funcion anterior
    public function cleanupExpiredSessionss() {
        $query = "DELETE FROM table_usuario_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 1 DAY)";
        return $this->delete($query);
    }










	public function getActiveSessionn($userId) {
		$this->userId = $userId;
        $query = "SELECT * FROM table_usuario_sessions 
                WHERE usuario_id = $this->userId AND last_activity > DATE_SUB(NOW(), INTERVAL 30 MINUTE) 
                ORDER BY last_activity DESC LIMIT 1";
        return $this->select($query, [$userId]);
    }



	public function createUser(int $intIdentificacion, string $strTxtNombre, string $strTxtEmail, string $strTxtPass){
		$this->intIdentificacion = $intIdentificacion;
		$this->strTxtNombre = $strTxtNombre;
		$this->strTxtPass = $strTxtPass;
		$this->strTxtEmail = $strTxtEmail;
		$this->intListStatus = 3;
		$this->intlistRolId = 3;
		$this->strImg = "default.png";

		//consultar si existe
		$sql = "SELECT * FROM table_user WHERE  user_email = '{$this->strTxtEmail}' or  user_ci = {$this->intIdentificacion}";
		$request = $this->select_all($sql);
		//si no encontro ningun resultado insertamos el usuario
		if(empty($request)){
			$queryInsert = "INSERT INTO table_user(user_ci,user_nombres,user_email,user_status,user_img, user_rol,user_pass) VALUES(?,?,?,?,?,?,?)";
			$arrData = array($this->intIdentificacion,$this->strTxtNombre,$this->strTxtEmail,$this->intListStatus,$this->strImg,$this->intlistRolId,$this->strTxtPass);
			$requestInsert = $this->insert($queryInsert,$arrData);
			$return = $requestInsert;
		}else{
			//si no viene vacia es que ya existe un registro
			$return = "exist";
		}
		return $return;
	}

	public function updateNick(int $intIdUser, int $intIdentificacion,string $strTxtEmail, string $strNick,string $strFileBase){
		$this->intIdUser = $intIdUser;
		$this->intIdentificacion = $intIdentificacion;
		$this->strTxtEmail = $strTxtEmail;
		$this->strNick = $strNick;
		$this->strFileBase = $strFileBase;
	 	$sql = "SELECT * FROM table_user WHERE user_email = '{$this->strTxtEmail}'  AND user_ci = $this->intIdentificacion ";
		$request = $this->select_all($sql);
		if(!empty($request)){
			$sql = "UPDATE table_user SET user_nick = ? , user_ruta = ? WHERE user_email = '{$this->strTxtEmail}'  AND user_ci = $this->intIdentificacion ";
			$arrData = array($this->strNick, $this->strFileBase);
			$request = $this->update($sql,$arrData);
		}else{
			$request = 'error';
		}
		return $request;
	}

	//crear la relacion user y rol al crearlo y se ingresa en 0 para despues asignarle
	public function setUserRol(int $intIdUser,int $intRol){
		$this->intRol = $intRol;
		$sql = "SELECT user_nick FROM table_user WHERE user_id = $intIdUser";
		$resp = $this->select($sql);
		if(!empty($resp)){
			$insertRol = "INSERT INTO table_user_rol(user_nick,id_rol) VALUES(?,?)";
			$arrDataRol = array($resp['user_nick'],$this->intRol);
			$request = $this->insert($insertRol,$arrDataRol);
		}else{
			$request ="error";
		}
		return $request;
	}
}