<?php
header('Access-Control-Allow-Origin: *');
class Menu extends Controllers{
	public function __construct(){
		session_start();
		if (empty($_SESSION['login']) && !isset($_SESSION['srtCodigo'])) {
			header("Location:".base_url().'login');
		}
		//invocar para que se ejecute el metodo de la herencia
		parent::__construct();
	}
	public function menu(){
		//invocar la vista con views y usamos getView y pasamos parametros esta clase y la vista
		//incluimos un arreglo que contendra toda la informacion que se enviara al home
		$data = [
			'page_tag' => "Pagina principal",
			'page_title' => "Pagina Principal",
			'page_name' => "menu",
			'page_link' => "menu",//activar el menu desplegable o un lin solo
			'page_menu_open' => "dashboard",//abrir el desplegable
			'page_link_acitvo' => "link-home",// seleccionar el link en el momento
			'page_functions' => "functionmenu.js"
		];
		$this->views->getViews($this, "menu", $data);
	}

	public function cargar_usuarios(){
        try {
            $usuarios = $this->model->cargar_usuarios();
            $arrResponse = array('status' => true, 'data' => $usuarios);
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error al cargar usuarios: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    
    public function get_user_info($usuario_id){
        try {
            $usuario_info = $this->model->get_user_info($usuario_id);
            $arrResponse = array('status' => true, 'data' => $usuario_info);
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error al cargar información del usuario: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    
    public function get_all_menus(){
        try {
            $menus = $this->model->get_all_menus();
            $submenus = $this->model->get_all_submenus();
            $arrResponse = array('status' => true, 'data' => array('menus' => $menus, 'submenus' => $submenus));
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error al cargar menús: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    
    public function get_user_permissions($usuario_id){
        try {
            $permisos = $this->model->get_user_permissions($usuario_id);
            $arrResponse = array('status' => true, 'data' => $permisos);
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error al cargar permisos: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    
    public function update_user_permissions(){
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validar que los datos necesarios estén presentes
            if (!isset($data['user_id']) || !isset($data['permissions'])) {
                throw new Exception("Datos incompletos");
            }
            
            $usuario_id = $data['user_id'];
            $permisos = $data['permissions'];
            
            // Validar tipos de datos
            if (!is_numeric($usuario_id) || !is_array($permisos)) {
                throw new Exception("Datos inválidos");
            }
            
            $result = $this->model->update_user_permissions($usuario_id, $permisos);

            if ($result) {
                $arrResponse = array('status' => true, 'msg' => 'Permisos actualizados correctamente');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al actualizar permisos');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error: ' . $e->getMessage());
        }
        
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

/********* crearemos las funciones para crear y eliminar menu *************/

    public function crear_menu(){
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($data['menu_nombre']) || empty($data['menu_icono'])){
                $arrResponse = array('status' => false, 'msg' => 'Nombre e icono son obligatorios');
                echo json_encode($arrResponse);
                die();
            }
            
            $result = $this->model->insertMenu($data);
            
            if($result > 0){
                $arrResponse = array('status' => true, 'msg' => 'Menú creado correctamente', 'menu_id' => $result);
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al crear menú');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function actualizar_menu(){
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($data['menu_id']) || empty($data['menu_nombre']) || empty($data['menu_icono'])){
                $arrResponse = array('status' => false, 'msg' => 'Datos incompletos');
                echo json_encode($arrResponse);
                die();
            }
            
            $result = $this->model->updateMenu($data);
            
            if($result){
                $arrResponse = array('status' => true, 'msg' => 'Menú actualizado correctamente');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al actualizar menú');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function eliminar_menu(){
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($data['menu_id'])){
                $arrResponse = array('status' => false, 'msg' => 'ID de menú requerido');
                echo json_encode($arrResponse);
                die();
            }

            $result = $this->model->deleteMenu($data['menu_id']);
            
            if($result){
                $arrResponse = array('status' => true, 'msg' => 'Menú eliminado correctamente');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar menú');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function listar_menus(){
        try {
            $menus = $this->model->getMenus();
            
            // Obtener submenús para cada menú
            foreach ($menus as &$menu) {
                if ($menu['menu_tiene_submenu']) {
                    $menu['submenus'] = $this->model->getSubmenusByMenu($menu['menu_id']);
                }
            }
            
            $arrResponse = array('status' => true, 'data' => $menus);
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error al cargar menús: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function crear_submenu(){
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($data['submenu_nombre']) || empty($data['submenu_pagina']) || empty($data['submenu_url']) || empty($data['menu_id'])){
                $arrResponse = array('status' => false, 'msg' => 'Todos los campos son obligatorios');
                echo json_encode($arrResponse);
                die();
            }
            
            $result = $this->model->insertSubmenu($data);
            
            if($result > 0){
                $arrResponse = array('status' => true, 'msg' => 'Submenú creado correctamente', 'submenu_id' => $result);
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al crear submenú');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function actualizar_submenu(){
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($data['submenu_id']) || empty($data['submenu_nombre']) || empty($data['submenu_pagina']) || empty($data['submenu_url'])){
                $arrResponse = array('status' => false, 'msg' => 'Datos incompletos');
                echo json_encode($arrResponse);
                die();
            }
            $result = $this->model->updateSubmenu($data);
            if($result){
                $arrResponse = array('status' => true, 'msg' => 'Submenú actualizado correctamente');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al actualizar submenú');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function eliminar_submenu(){
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($data['submenu_id'])){
                $arrResponse = array('status' => false, 'msg' => 'ID de submenú requerido');
                echo json_encode($arrResponse);
                die();
            }

            $result = $this->model->deleteSubmenu($data['submenu_id']);
            
            if($result){
                $arrResponse = array('status' => true, 'msg' => 'Submenú eliminado correctamente');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar submenú');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function get_submenus_by_menu($menu_id){
        try {
            $submenus = $this->model->getSubmenusByMenu($menu_id);
            $arrResponse = array('status' => true, 'data' => $submenus);
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error al cargar submenús: ' . $e->getMessage());
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

}