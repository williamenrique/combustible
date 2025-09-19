<?php
class UserModel extends Mysql {
    private $tablaUsuarios = "table_usuarios";
    
    public function __construct(){
        parent::__construct();
    }
    
    /****** subir imagen de perfil *******/
    public function updateImg(int $intIdUser, string $fileBase){
        $sql = "UPDATE {$this->tablaUsuarios} SET usuario_imagen = ? WHERE usuario_id = ?";
        return $this->update($sql, [$fileBase, $intIdUser]);
    }
    
    public function selectUsuario(int $id_usuario) {
        $sql = "SELECT usuario_id, usuario_password FROM {$this->tablaUsuarios} WHERE usuario_id = ? AND usuario_status = 1";
        return $this->select($sql, [$id_usuario]);
    }
    
    /******** actualizar password ********/
    public function updatePassword(int $intIdUser, string $hashedPassword) {
        $sql = "UPDATE {$this->tablaUsuarios} SET usuario_password = ? WHERE usuario_id = ?";
        return $this->update($sql, [$hashedPassword, $intIdUser]);
    }
    
    /******** actualizar datos del usuario ********/
    public function updateUserData($id_usuario, $data) {    
        $sql = "UPDATE {$this->tablaUsuarios} SET 
                usuario_nombres = ?, 
                usuario_apellidos = ?, 
                usuario_email = ?, 
                usuario_telefono = ?, 
                usuario_direccion = ?
                WHERE usuario_id = ?";
        
        $arrData = [
            $data['usuario_nombres'],
            $data['usuario_apellidos'],
            $data['usuario_email'],
            $data['usuario_telefono'],
            $data['usuario_direccion'],
            $id_usuario
        ];
        // echo $this->debugQuery($sql, $arrData);
        return $this->update($sql, $arrData);
    }
    
    // comprobacion que el email no este ya en uso
    public function checkEmailExists($email, $exclude_user_id = 0) {
        $sql = "SELECT usuario_id FROM {$this->tablaUsuarios} WHERE usuario_email = ? AND usuario_id != ? AND usuario_status = 1";
        return $this->select($sql, [$email, $exclude_user_id]);
    }
    
    // Verificar si identificación existe
    public function checkIdExists($identificacion) {
        $sql = "SELECT usuario_id 
                FROM table_usuarios
                WHERE usuario_ci = ? 
                AND usuario_status != 0";
        $request = $this->select($sql, [$identificacion]);
        return !empty($request);
    }
    
    /******* creacion de usuarios ******/
    // Obtener roles
    public function getRoles() {
        $sql = "SELECT rol_id as id, rol_nombre as nombre 
                FROM table_roles 
                WHERE rol_status = 1 
                ORDER BY rol_nombre";
        return $this->select_all($sql);
    }
    
    // Obtener departamentos
    public function getDepartments() {
        $sql = "SELECT departamento_id as id, departamento_nombre as nombre 
                FROM table_departamentos
                WHERE departamento_status = 1 
                ORDER BY departamento_nombre";
        return $this->select_all($sql);
    }
    
    /******** insertar usuario ********/
    // Insertar usuario (versión optimizada)
    public function insertUser($identificacion, $nombre, $intlistRolId, $intlistDep, $apellidos, $telefono, $email, $direccion, $password) {
        // Verificar si el usuario ya existe
        $sql_check = "SELECT usuario_id 
                    FROM table_usuarios 
                    WHERE (usuario_ci = ? OR usuario_email = ?) 
                    AND usuario_status != 0";
        $request_check = $this->select($sql_check, [$identificacion, $email]);
        
        if (!empty($request_check)) {
            return "exist";
        }

        // Insertar nuevo usuario
        $sql = "INSERT INTO table_usuarios (usuario_password, usuario_nombres, usuario_apellidos, 
                                    usuario_email, usuario_telefono, usuario_rol_id, usuario_departamento_id, 
                                    usuario_direccion, usuario_status, usuario_creado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
        
        $request = $this->insert($sql, [$password, $nombre, $apellidos, $email, $telefono, $intlistRolId, $intlistDep, $direccion]);
        
        return $request;
    }
    
    // Crear nick y actualizar información
    public function createNick($userId, $identificacion, $email, $nick, $rolId, $fileBase, $depId) {
        $sql = "UPDATE table_usuarios 
                SET usuario_nick = ?, 
                    usuario_imagen = ?,
                    usuario_ci = ?,
                    usuario_email = ?
                WHERE usuario_id = ?";
        
        return $this->update($sql, [$nick, $fileBase.'default.png', $identificacion, $email, $userId]);
    }
    
    /****** mostrar usuarios en tabla y sus funciones ********/
    // Obtener todos los usuarios
    public function getUsuarios() {
        $sql = "SELECT u.usuario_id, u.usuario_ci, u.usuario_nombres, u.usuario_apellidos, 
                    u.usuario_email, u.usuario_telefono, u.usuario_direccion, u.usuario_status,
                    r.rol_nombre, d.departamento_nombre
                FROM {$this->tablaUsuarios} u
                INNER JOIN table_roles r ON u.usuario_rol_id = r.rol_id
                INNER JOIN table_departamentos d ON u.usuario_departamento_id = d.departamento_id
                WHERE u.usuario_status IN (0, 1)
                ORDER BY u.usuario_id DESC";
        
        return $this->select_all($sql);
    }

    // Obtener un usuario específico
    public function getUsuario($idUsuario) {
        $sql = "SELECT u.*, r.rol_nombre, d.departamento_nombre
                FROM {$this->tablaUsuarios} u
                INNER JOIN table_roles r ON u.usuario_rol_id = r.rol_id
                INNER JOIN table_departamentos d ON u.usuario_departamento_id = d.departamento_id
                WHERE u.usuario_id = ?";
        
        return $this->select($sql, [$idUsuario]);
    }

    /***** Actualizar usuario ******/
    public function updateUsuario($data) {
        $sql = "UPDATE {$this->tablaUsuarios} SET 
                usuario_ci = ?,
                usuario_nombres = ?,
                usuario_apellidos = ?,
                usuario_email = ?,
                usuario_telefono = ?,
                usuario_direccion = ?,
                usuario_rol_id = ?,
                usuario_departamento_id = ?,
                usuario_status = ?
                WHERE usuario_id = ?";
        
        $arrData = [
            $data['usuario_ci'],
            $data['usuario_nombres'],
            $data['usuario_apellidos'],
            $data['usuario_email'],
            $data['usuario_telefono'],
            $data['usuario_direccion'],
            $data['usuario_rol_id'],
            $data['usuario_departamento_id'],
            $data['usuario_status'],
            $data['usuario_id']
        ];
        
        return $this->update($sql, $arrData);
    }

    // Actualizar estado del usuario
    public function updateStatus($idUsuario, $status) {
        $sql = "UPDATE {$this->tablaUsuarios} SET usuario_status = ? WHERE usuario_id = ?";
        return $this->update($sql, [$status, $idUsuario]);
    }
    /**********
     * metodos para departamentos en usuarios
     */
    public function selectDepartamentos() {
        $sql = "SELECT * FROM table_departamentos WHERE departamento_status != 2"; // 2 = eliminado
        return $this->select_all($sql);
    }

    public function selectDepartamento(int $iddepto) {
        $sql = "SELECT * FROM table_departamentos WHERE departamento_id = ?";
        return $this->select($sql, [$iddepto]);
    }

    public function insertDepartamento(string $nombre, string $descripcion, int $status) {
        $return = 0;
        $sql = "SELECT * FROM table_departamentos WHERE departamento_nombre = ?";
        $request = $this->select_all($sql, [$nombre]);

        if (empty($request)) {
            $query_insert = "INSERT INTO table_departamentos(departamento_nombre, departamento_descripcion, departamento_status) VALUES(?,?,?)";
            $arrData = [$nombre, $descripcion, $status];
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function updateDepartamento(int $iddepto, string $nombre, string $descripcion, int $status) {
        $sql = "UPDATE table_departamentos SET departamento_nombre = ?, departamento_descripcion = ?, departamento_status = ? WHERE departamento_id = ?";
        $arrData = [$nombre, $descripcion, $status, $iddepto];
        return $this->update($sql, $arrData);
    }

    public function deleteDepartamento(int $iddepto) {
        $sql_check = "SELECT COUNT(*) as total FROM table_usuarios WHERE usuario_departamento_id = ? AND usuario_status = 1";
        $request_check = $this->select($sql_check, [$iddepto]);

        if(isset($request_check['total']) && $request_check['total'] > 0){
            return 'in_use';
        }

        // Eliminación lógica
        $sql = "UPDATE table_departamentos SET departamento_status = 0 WHERE departamento_id = ?";
        return $this->update($sql, [$iddepto]);
    }

    /*****
	* CRUD de Roles
	*****/
    public function selectRoles() {
        $sql = "SELECT * FROM table_roles WHERE rol_status != 2"; // 2 = eliminado
        return $this->select_all($sql);
    }

    public function selectRol(int $idrol) {
        $sql = "SELECT * FROM table_roles WHERE rol_id = ?";
        return $this->select($sql, [$idrol]);
    }

    public function insertRol(string $nombre, string $descripcion, int $status) {
        $return = 0;
        $sql = "SELECT * FROM table_roles WHERE rol_nombre = ?";
        $request = $this->select_all($sql, [$nombre]);

        if (empty($request)) {
            $query_insert = "INSERT INTO table_roles(rol_nombre, rol_descripcion, rol_status) VALUES(?,?,?)";
            $arrData = [$nombre, $descripcion, $status];
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function updateRol(int $idrol, string $nombre, string $descripcion, int $status) {
        $sql = "UPDATE table_roles SET rol_nombre = ?, rol_descripcion = ?, rol_status = ? WHERE rol_id = ?";
        $arrData = [$nombre, $descripcion, $status, $idrol];
        return $this->update($sql, $arrData);
    }

    public function deleteRol(int $idrol) {
        $sql_check = "SELECT COUNT(*) as total FROM table_usuarios WHERE usuario_rol_id = ? AND usuario_status = 1";
        $request_check = $this->select($sql_check, [$idrol]);
        if(isset($request_check['total']) && $request_check['total'] > 0){
            return 'in_use';
        }
        $sql = "UPDATE table_roles SET rol_status = 0 WHERE rol_id = ?";
        return $this->update($sql, [$idrol]);
    }
}