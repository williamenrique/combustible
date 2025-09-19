<?php
header('Access-Control-Allow-Origin: *');
class Bienes extends Controllers{
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
            // Para vistas HTML, podrías guardar el error para mostrarlo
            $_SESSION['error_message'] = "Error de base de datos: " . $error;
        }
    }

    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    /**fin de manejo de errores en cada controlador debe estar*/
    public function bienes(){

        // Validar nuevamente la sesión antes de mostrar el home
        if (!$this->validateSession()) {
            header("Location:".base_url().'login');
            exit();
        }
        $data = [
            'page_tag' => "Pagina principal",
            'page_title' => "Pagina Principal",
            'page_name' => "bienes",
            'page_link' => "bienes",
            'page_functions' => "function.bienes.js"
        ];
        $data['departamentos'] = $this->model->getDepartamentos(); // Añadido para pasar los departamentos a la vista
        $this->views->getViews($this, "bienes", $data);
    }

    // Método para obtener datos iniciales para los formularios
    public function getInitialData() {
        try {
            $data['departamentos'] = $this->model->getDepartamentos();
            $data['grupos'] = $this->model->getGrupos();
            $data['subgrupos'] = $this->model->getSubgrupos();
            $data['secciones'] = $this->model->getSecciones();
            $arrResponse = ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            $arrResponse = ['success' => false, 'message' => $e->getMessage()];
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Método para obtener todos los bienes
    public function getBienes() {
        try {
            $arrData = $this->model->selectBienes();
            // Bucle para agregar los botones de acción a cada registro
            for ($i=0; $i < count($arrData); $i++) {
                $btnEdit = '';
                $btnDelete = '';
                // Botones de acción con el estilo de Tailwind CSS
                $btnEdit = '<button class="px-2 py-1 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600" onClick="fntEditBien('.$arrData[$i]['id_bien'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                $btnDelete = '<button class="px-2 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600" onClick="fntDelBien('.$arrData[$i]['id_bien'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
                $arrData[$i]['acciones'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDelete . '</div>';
            }
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $this->handleDatabaseError($e->getMessage());
        }
        die();
    }

    // Método para obtener un bien específico
    public function getBien($id_bien) {
        try {
            $id_bien = intval($id_bien);
            if ($id_bien > 0) {
                $arrData = $this->model->selectBien($id_bien);
                if (empty($arrData)) {
                    $arrResponse = ['success' => false, 'message' => 'Datos no encontrados.'];
                } else {
                    // Normalizar el formato de la fecha antes de enviarla al frontend.
                    // El input type="date" espera el formato YYYY-MM-DD.
                    if (!empty($arrData['fecha_adquisicion']) && $arrData['fecha_adquisicion'] != '0000-00-00') {
                        // Intentar crear un objeto de fecha desde el formato DD/M/YYYY
                        $date = DateTime::createFromFormat('d/m/Y', $arrData['fecha_adquisicion']);
                        
                        // Si falla, intentar con otros formatos comunes (YYYY-MM-DD con o sin hora)
                        if ($date === false) {
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $arrData['fecha_adquisicion']);
                            if ($date === false) {
                                $date = DateTime::createFromFormat('Y-m-d', $arrData['fecha_adquisicion']);
                            }
                        }
                        // Si se pudo parsear la fecha, formatearla. Si no, dejarla vacía.
                        $arrData['fecha_adquisicion'] = ($date) ? $date->format('Y-m-d') : '';
                    } else {
                        $arrData['fecha_adquisicion'] = ''; // Limpiar si es nula o inválida
                    }
                    $arrResponse = ['success' => true, 'data' => $arrData];
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            $this->handleDatabaseError($e->getMessage());
        }
        die();
    }

    // Método para insertar o actualizar un bien
    public function setBien() {
        if ($_POST) {
            try {
                $id_bien = intval($_POST['id_bien'] ?? 0);
                $data = [
                    'bien_depatamento_id' => strClean($_POST['departamento']),
                    'grupo_id' => strClean($_POST['grupo']),
                    'subgrupo_id' => strClean($_POST['subgrupo']),
                    'seccion_id' => strClean($_POST['seccion']),
                    'descripcion_bien' => strClean($_POST['descripcion']),
                    'fecha_adquisicion' => strClean($_POST['fecha_adquisicion']),
                    'status_bien' => strClean($_POST['status_bien']),
                    'user_id' => $_SESSION['idUser']
                ];

                if ($id_bien == 0) {
                    // Crear bien
                    $request_bien = $this->model->insertBien($data);
                    $option = 1;
                } else {
                    // Actualizar bien
                    $data['id_bien'] = $id_bien;
                    $request_bien = $this->model->updateBien($data);
                    $option = 2;
                }

                if ($request_bien > 0) {
                    if ($option == 1) {
                        $arrResponse = ['success' => true, 'message' => 'Bien guardado correctamente.'];
                    } else {
                        $arrResponse = ['success' => true, 'message' => 'Bien actualizado correctamente.'];
                    }
                } else {
                    $arrResponse = ['success' => false, 'message' => 'No es posible almacenar los datos.'];
                }
            } catch (Exception $e) {
                $arrResponse = ['success' => false, 'message' => 'Error en el proceso: ' . $e->getMessage()];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // Método para eliminar un bien
    public function delBien() {
        if ($_POST) {
            try {
                $id_bien = intval($_POST['id_bien']);
                $requestDelete = $this->model->deleteBien($id_bien);
                if ($requestDelete) {
                    $arrResponse = ['success' => true, 'message' => 'Se ha eliminado el bien'];
                } else {
                    $arrResponse = ['success' => false, 'message' => 'Error al eliminar el bien.'];
                }
            } catch (Exception $e) {
                $arrResponse = ['success' => false, 'message' => 'Error en el proceso: ' . $e->getMessage()];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}