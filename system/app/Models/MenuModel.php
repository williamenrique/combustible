<?php
class MenuModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
    public function obtenerMenuUsuario($usuarioNick) {
        $this->usuarioNick = $usuarioNick;
        $sql = "SELECT 
            m.menu_id, m.menu_nombre, m.menu_icono, m.menu_tiene_submenu, m.menu_pagina, 
            s.submenu_id, s.submenu_nombre, s.submenu_pagina, s.submenu_url, r.rol_id 
            FROM table_usuarios u 
            INNER JOIN table_roles r ON u.usuario_rol_id = r.rol_id 
            INNER JOIN table_permisos_rol_menu prm ON r.rol_id = prm.rol_id 
            INNER JOIN table_menu m ON prm.menu_id = m.menu_id 
            LEFT JOIN table_submenu s ON prm.submenu_id = s.submenu_id 
            WHERE u.usuario_nick = ?
                    AND u.usuario_status = 1 
                    AND m.menu_status = 1 
                    AND (s.submenu_status = 1 OR s.submenu_status IS NULL)
                    AND prm.permiso_status = 1
            ORDER BY m.menu_orden ASC, s.submenu_orden ASC";
        
        // Esta función select_all debe estar implementada en tu modelo base
        $request = $this->select_all($sql, [$this->usuarioNick]);
        return $request;
    }
    // Función para obtener todos los menús (para administración)
    public function obtenerTodosMenus() {
        $sql = "SELECT * FROM table_menu WHERE menu_status = 1 ORDER BY menu_orden ASC";
        return $this->select_all($sql);
    }
    // Función para obtener todos los submenús (para administración)
    public function obtenerTodosSubmenus() {
        $sql = "SELECT * FROM table_submenu WHERE submenu_status = 1 ORDER BY submenu_orden ASC";
        return $this->select_all($sql);
    }
    // TODO: comienza para la carga de asignacion de menu
    public function cargar_usuarios() {
        $sql = "SELECT u.usuario_id, u.usuario_nick, u.usuario_nombres, u.usuario_apellidos, 
                r.rol_id, r.rol_nombre 
                FROM table_usuarios u 
                INNER JOIN table_roles r ON u.usuario_rol_id = r.rol_id 
                WHERE u.usuario_status = 1 
                ORDER BY u.usuario_nombres, u.usuario_apellidos";
        return $this->select_all($sql);
    }
    
    public function get_user_info($usuario_id) {
        $sql = "SELECT u.*, r.rol_nombre, d.departamento_nombre 
                FROM table_usuarios u 
                INNER JOIN table_roles r ON u.usuario_rol_id = r.rol_id 
                INNER JOIN table_departamentos d ON u.usuario_departamento_id = d.departamento_id 
                WHERE u.usuario_id = ?";
        return $this->select($sql, [$usuario_id]);
    }
    
    public function get_all_menus() {
        $sql = "SELECT * FROM table_menu WHERE menu_status = 1 ORDER BY menu_orden";
        return $this->select_all($sql);
    }
    
    public function get_all_submenus() {
        $sql = "SELECT sm.*, msm.menu_id 
                FROM table_submenu sm 
                INNER JOIN table_menu_submenu msm ON sm.submenu_id = msm.submenu_id 
                WHERE sm.submenu_status = 1 
                ORDER BY sm.submenu_orden";
        return $this->select_all($sql);
    }
    
    public function get_user_permissions($usuario_id) {
        $sql = "SELECT r.rol_id, p.menu_id, p.submenu_id 
                FROM table_permisos_rol_menu p 
                INNER JOIN table_roles r ON p.rol_id = r.rol_id 
                INNER JOIN table_usuarios u ON u.usuario_rol_id = r.rol_id 
                WHERE u.usuario_id = $usuario_id AND p.permiso_status = 1
                UNION
                SELECT r.rol_id, p.menu_id, p.submenu_id 
                FROM table_permisos_rol_menu p 
                INNER JOIN table_roles r ON p.rol_id = r.rol_id 
                WHERE r.rol_id = (SELECT usuario_rol_id FROM table_usuarios WHERE usuario_id = ?) 
                AND p.permiso_status = 1";
        return $this->select_all($sql, [$usuario_id]);
    }
    
    public function update_user_permissions($usuario_id, $permisos) {
        try {
            // Validar que el usuario_id sea numérico
            if (!is_numeric($usuario_id)) {
                throw new Exception("ID de usuario inválido");
            }
            // Primero obtener el rol del usuario con consulta preparada
            $sql = "SELECT usuario_rol_id FROM table_usuarios WHERE usuario_id = ?";
            $usuario = $this->select($sql, [$usuario_id]);
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }
            $rol_id = $usuario['usuario_rol_id'];
            // Iniciar transacción para asegurar consistencia
            // $this->start_transaction();
            // Eliminar todos los permisos actuales del rol (solo los de menú/submenú)
            $sql_delete = "DELETE FROM table_permisos_rol_menu WHERE rol_id = ?";
            $this->delete($sql_delete, [$rol_id]);

            // Insertar los nuevos permisos con consultas preparadas
            foreach ($permisos as $permiso) {
                // Validar que menu_id sea numérico
                if (!isset($permiso['menu_id']) || !is_numeric($permiso['menu_id'])) {
                    throw new Exception("Menu ID inválido en los permisos");
                }
                $menu_id = intval($permiso['menu_id']);
                // Manejar submenu_id correctamente (puede ser null)
                $submenu_id = null;
                if (isset($permiso['submenu_id']) && !empty($permiso['submenu_id']) && $permiso['submenu_id'] !== 'null') {
                    if (!is_numeric($permiso['submenu_id'])) {
                        throw new Exception("Submenu ID inválido en los permisos");
                    }
                    $submenu_id = intval($permiso['submenu_id']);
                }
                // Consulta preparada para inserción segura
                $sql_insert = "INSERT INTO table_permisos_rol_menu (rol_id, menu_id, submenu_id, permiso_status) 
                               VALUES (?, ?, ?, 1)";
                $this->insert($sql_insert, [$rol_id, $menu_id, $submenu_id]);
            }
            return true;
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if ($this->start_transaction()) {
                $this->RollBack();
            }
            return false;
        }
    }
    // TODO: comienza para la creacion de menu

    public function insertMenu($data) {
        $query = "INSERT INTO table_menu (menu_nombre, menu_icono, menu_tiene_submenu, menu_pagina, menu_orden) 
                  VALUES (?, ?, ?, ?, ?)";
        $arrData = array(
            $data['menu_nombre'], 
            $data['menu_icono'], 
            $data['menu_tiene_submenu'] ? 1 : 0, 
            $data['menu_tiene_submenu'] ? NULL : $data['menu_pagina'], 
            $data['menu_orden']
        );
        return $this->insert($query, $arrData);
    }

    public function updateMenu($data) {
        $query = "UPDATE table_menu 
                  SET menu_nombre = ?, menu_icono = ?, menu_tiene_submenu = ?, 
                      menu_pagina = ?, menu_orden = ? 
                  WHERE menu_id = ?";
        $arrData = array(
            $data['menu_nombre'], 
            $data['menu_icono'], 
            $data['menu_tiene_submenu'] ? 1 : 0, 
            $data['menu_tiene_submenu'] ? NULL : $data['menu_pagina'], 
            $data['menu_orden'],
            $data['menu_id']
        );
        return $this->update($query, $arrData);
    }

    public function deleteMenu($menu_id) {
        $query = "UPDATE table_menu SET menu_status = 0 WHERE menu_id = ?";
        $arrData = array($menu_id);
        return $this->update($query, $arrData);
    }

    public function getMenus() {
        $query = "SELECT * FROM table_menu WHERE menu_status = 1 ORDER BY menu_orden ASC";
        return $this->select_all($query);
    }

    public function insertSubmenu($data) {
        // Primero insertar en table_submenu
        $query = "INSERT INTO table_submenu (submenu_nombre, submenu_pagina, submenu_url, submenu_orden) 
                  VALUES (?, ?, ?, ?)";
        $arrData = array(
            $data['submenu_nombre'], 
            $data['submenu_pagina'], 
            $data['submenu_url'], 
            $data['submenu_orden']
        );
        $submenu_id = $this->insert($query, $arrData);
        if ($submenu_id > 0) {
            // Luego insertar la relación en table_menu_submenu
            $query_rel = "INSERT INTO table_menu_submenu (menu_id, submenu_id) VALUES (?, ?)";
            $arrData_rel = array($data['menu_id'], $submenu_id);
            $this->insert($query_rel, $arrData_rel);
            // Finalmente insertar en table_permisos_rol_menu para los roles
            $query_perm = "INSERT INTO table_permisos_rol_menu (rol_id, menu_id, submenu_id) 
                           SELECT rol_id, ?, ? FROM table_roles WHERE rol_status = 1";
            $arrData_perm = array($data['menu_id'], $submenu_id);
            $this->insert($query_perm, $arrData_perm);
        }
        return $submenu_id;
    }

    public function getSubmenus() {
        $query = "SELECT s.*, m.menu_id, m.menu_nombre 
                FROM table_submenu s 
                INNER JOIN table_menu_submenu ms ON s.submenu_id = ms.submenu_id 
                INNER JOIN table_menu m ON ms.menu_id = m.menu_id 
                WHERE s.submenu_status = 1 AND m.menu_status = 1 
                ORDER BY m.menu_orden ASC, s.submenu_orden ASC";
        return $this->select_all($query);
    }

    public function getMenuConSubmenus($menu_id) {
        $query = "SELECT m.*, 
                (SELECT COUNT(*) FROM table_menu_submenu ms 
                INNER JOIN table_submenu s ON ms.submenu_id = s.submenu_id 
                WHERE ms.menu_id = m.menu_id AND s.submenu_status = 1) as total_submenus
                FROM table_menu m 
                WHERE m.menu_id = ? AND m.menu_status = 1";
        $arrData = array($menu_id);
        return $this->select($query, $arrData);
    }

    public function getSubmenusPorMenu($menu_id) {
        $query = "SELECT s.* 
                  FROM table_submenu s 
                  INNER JOIN table_menu_submenu ms ON s.submenu_id = ms.submenu_id 
                  WHERE ms.menu_id = ? AND s.submenu_status = 1 
                  ORDER BY s.submenu_orden ASC";
        $arrData = array($menu_id);
        return $this->select_all($query, $arrData);
    }

    public function deleteSubmenu($submenu_id) {
        $query = "UPDATE table_submenu SET submenu_status = 0 WHERE submenu_id = ?";
        $arrData = array($submenu_id);
        return $this->update($query, $arrData);
    }

    public function getSubmenusByMenu($menu_id) {
        $query = "SELECT s.* 
                FROM table_submenu s 
                INNER JOIN table_menu_submenu ms ON s.submenu_id = ms.submenu_id 
                WHERE ms.menu_id = ? AND s.submenu_status = 1 
                ORDER BY s.submenu_orden ASC";
        $arrData = array($menu_id);
        return $this->select_all($query, $arrData);
    }

    public function updateSubmenu($data) {
        $query = "UPDATE table_submenu 
                SET submenu_nombre = ?, submenu_pagina = ?, submenu_url = ?, submenu_orden = ? 
                WHERE submenu_id = ?";
        $arrData = array(
            $data['submenu_nombre'], 
            $data['submenu_pagina'], 
            $data['submenu_url'], 
            $data['submenu_orden'],
            $data['submenu_id']
        );
        return $this->update($query, $arrData);
    }
}