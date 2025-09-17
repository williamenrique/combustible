<?php
// Retorna la ruta del proyecto
function base_url() {
    return defined('BASE_URL') ? BASE_URL : '';
}

function head($data = "") {
    $view_header = VIEWS . "Modules/header.php";
    if (file_exists($view_header)) {
        require_once $view_header;
    }
}

function footer($data = "") {
    $view_footer = VIEWS . "Modules/footer.php";
    if (file_exists($view_footer)) {
        require_once $view_footer;
    }
}

// Muestra información formateada
function dep($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function encryption($string) {
    if (empty($string)) return '';
    
    $output = false;
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    $output = openssl_encrypt($string, METHOD, $key, 0, $iv);
    return $output ? base64_encode($output) : '';
}

function decryption($string) {
    if (empty($string)) return '';
    
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    $output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
    return $output ?: '';
}

function formatear_timestamp($fecha) {
    if (empty($fecha)) return '';
    
    $timestamp = is_numeric($fecha) ? $fecha : strtotime($fecha);
    if ($timestamp === false) return '';
    
    $dias = ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"];
    $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", 
              "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    
    $dia_semana = $dias[date('w', $timestamp)];
    $dia = date('d', $timestamp);
    $mes = $meses[date('n', $timestamp) - 1];
    $hora = date('G:i a', $timestamp);
    
    return "{$dia_semana}, {$dia} de {$mes} a las {$hora}";
}

function formatear_fecha($fecha) {
    if (empty($fecha)) return '';
    
    $timestamp = strtotime($fecha);
    if ($timestamp === false) return '';
    
    $dias = ["Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom"];
    $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", 
              "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    
    $dia_semana = $dias[date('N', $timestamp) - 1];
    $dia = date('d', $timestamp);
    $mes = $meses[date('n', $timestamp) - 1];
    $ano = date('Y', $timestamp);
    
    return "{$dia_semana}, {$dia} de {$mes} · {$ano}";
}

function sessionUser(int $idUser) {
    if ($idUser <= 0) return null;
    
    $modelPath = "system/app/Models/LoginModel.php";
    if (file_exists($modelPath)) {
        require_once $modelPath;
        $objLogin = new LoginModel();
        return $objLogin->sessionLogin($idUser);
    }
    return null;
}

function getActiveSession(int $intIdUser) {
    if ($intIdUser <= 0) return null;
    
    $modelPath = "system/app/Models/LoginModel.php";
    if (file_exists($modelPath)) {
        require_once $modelPath;
        $objSession = new LoginModel();
        return $objSession->getActiveSession($intIdUser);
    }
    return null;
}

function validateSessionDB(string $idSesion, int $intIdUser) {
    if (empty($idSesion) || $intIdUser <= 0) return false;
    
    $modelPath = "system/app/Models/LoginModel.php";
    if (file_exists($modelPath)) {
        require_once $modelPath;
        $objValidar = new LoginModel();
        return (bool) $objValidar->validateSessionDB($idSesion, $intIdUser);
    }
    return false;
}

function deleteSession(string $idSession = null) {
    if ($idSession) {
        $modelPath = "system/app/Models/LoginModel.php";
        if (file_exists($modelPath)) {
            require_once $modelPath;
            $objDestroy = new LoginModel();
            $objDestroy->deleteSession($idSession);
        }
    }
    
    destroySession();
}

function destroySession() {
    $_SESSION = [];
    
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], 
            $params['domain'],
            $params['secure'], 
            $params['httponly']
        );
    }
    
    session_destroy();
}

function strClean($strCadena) {
    if (empty($strCadena)) return '';
    
    // Limpieza básica
    $string = trim($strCadena);
    $string = stripslashes($string);
    $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    
    // Patrones de inyección SQL
    $patterns = [
        '/<script.*?>.*?<\/script>/is',
        '/SELECT.*?FROM/i',
        '/DELETE.*?FROM/i',
        '/INSERT INTO/i',
        '/DROP TABLE/i',
        '/UPDATE.*?SET/i',
        '/OR \'1\'=\'1\'/i',
        '/OR "1"="1"/i',
        '/--/',
        '/#/',
        '/\/\*/',
        '/\*\//',
        '/UNION.*?SELECT/i'
    ];
    
    $string = preg_replace($patterns, '', $string);
    
    // Caracteres peligrosos
    $string = str_replace(['^', '[', ']', '==', ';'], '', $string);
    
    return $string;
}

function passGenerator($length = 12) {
    if ($length < 8) $length = 8;
    
    $chars = [
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'abcdefghijklmnopqrstuvwxyz',
        '0123456789',
        '!@#$%^&*()_+-=[]{}|;:,.<>?'
    ];
    
    $password = '';
    
    // Asegurar al menos un carácter de cada tipo
    foreach ($chars as $charSet) {
        $password .= $charSet[random_int(0, strlen($charSet) - 1)];
    }
    
    // Completar con caracteres aleatorios
    $allChars = implode('', $chars);
    for ($i = strlen($password); $i < $length; $i++) {
        $password .= $allChars[random_int(0, strlen($allChars) - 1)];
    }
    
    // Mezclar la contraseña
    return str_shuffle($password);
}

function codGenerator($length = 6) {
    if ($length < 4) $length = 4;
    
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    
    return $code;
}

function token() {
    return sprintf('%s-%s-%s-%s',
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4))
    );
}

function versql($sql, $arrData) {
    $sqlDebug = $sql;
    
    foreach ($arrData as $valor) {
        $valorFormateado = is_numeric($valor) ? $valor : "'" . addslashes($valor) . "'";
        $sqlDebug = preg_replace('/\?/', $valorFormateado, $sqlDebug, 1);
    }
    
    error_log("SQL DEBUG: " . $sqlDebug);
    
    if (defined('DEBUG') && DEBUG) {
        echo "<pre style='background: #f4f4f4; padding: 10px; border: 1px solid #ccc;'>SQL: " . htmlspecialchars($sqlDebug) . "</pre>";
    }
}

function validarCaracteres($name) {
    if (empty($name)) return '';
    
    $caracteresPermitidos = "0123456789_-.@$()={}[]° abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZáéíóúÁÉÍÓÚñÑ";
    $resultado = '';
    
    $caracteresArray = preg_split('//u', $name, -1, PREG_SPLIT_NO_EMPTY);
    $permitidosArray = preg_split('//u', $caracteresPermitidos, -1, PREG_SPLIT_NO_EMPTY);
    
    foreach ($caracteresArray as $caracter) {
        if (in_array($caracter, $permitidosArray)) {
            $resultado .= $caracter;
        }
    }
    
    return $resultado;
}

function cargar_menu_usuarios($usuarioNick) {
    require_once("system/app/Models/MenuModel.php");
    $menuModel = new MenuModel();
    $menuData = $menuModel->obtenerMenuUsuario($usuarioNick);
    
    if (empty($menuData)) {
        echo '<div class="alert alert-warning">No tiene permisos para acceder a ningún menú</div>';
        return;
    }
    
    // Organizar los datos en una estructura jerárquica
    $menuEstructura = array();
    foreach ($menuData as $item) {
        $menuId = $item['menu_id'];
        
        if (!isset($menuEstructura[$menuId])) {
            $menuEstructura[$menuId] = array(
                'menu_id' => $item['menu_id'],
                'menu_nombre' => $item['menu_nombre'],
                'menu_icono' => $item['menu_icono'],
                'menu_tiene_submenu' => $item['menu_tiene_submenu'],
                'menu_pagina' => $item['menu_pagina'],
                'submenus' => array()
            );
        }
        
        // Solo agregar submenús si existen
        if (!empty($item['submenu_id']) && !empty($item['submenu_nombre'])) {
            $menuEstructura[$menuId]['submenus'][] = array(
                'submenu_id' => $item['submenu_id'],
                'submenu_nombre' => $item['submenu_nombre'],
                'submenu_pagina' => $item['submenu_pagina'],
                'submenu_url' => $item['submenu_url']
            );
        }
    }
    
    // Generar el HTML del menú
    echo '<nav class="sidebar-menu">
            <ul>';
    
    // Función auxiliar para crear slugs
    function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return $text;
    }
    
    // Generar los ítems de menú según la estructura
    foreach ($menuEstructura as $menu) {
        $tieneSubmenus = !empty($menu['submenus']) && $menu['menu_tiene_submenu'];
        $claseMenu = $tieneSubmenus ? 'menu-item has-submenu' : 'menu-item';
        $dataMenu = $menu['menu_pagina'] ?: slugify($menu['menu_nombre']);
        
        echo '<li class="' . $claseMenu . '" data-menu="' . $dataMenu . '">';
        
        if ($tieneSubmenus) {
            echo '<a href="#" class="menu-link">
                    <span class="menu-icon"><i class="' . $menu['menu_icono'] . '"></i></span>
                    <span class="menu-text">' . $menu['menu_nombre'] . '</span>
                    <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                  </a>';
            
            echo '<ul class="submenu">';
            foreach ($menu['submenus'] as $submenu) {
                $submenuSlug = slugify($submenu['submenu_nombre']);
                echo '<li>
                        <a href="' . base_url() . $submenu['submenu_url'] . '" 
                           class="submenu-link" 
                           data-page="' . $submenu['submenu_pagina'] . '" 
                           data-submenu="' . $submenuSlug . '">
                           ' . $submenu['submenu_nombre'] . '
                        </a>
                      </li>';
            }
            echo '</ul>';
        } else {
            // Menú sin submenús - enlace directo
            $enlace = $menu['menu_pagina'] ? base_url() . $menu['menu_pagina'] : '#';
            echo '<a href="' . $enlace . '" class="menu-link" data-page="' . $menu['menu_pagina'] . '">
                    <span class="menu-icon"><i class="' . $menu['menu_icono'] . '"></i></span>
                    <span class="menu-text">' . $menu['menu_nombre'] . '</span>
                  </a>';
        }
        
        echo '</li>';
    }
    
    // Ítem de Cerrar Sesión (siempre visible si el usuario está logueado)
    echo '<li class="menu-item" data-menu="cerrar-sesion">
            <a href="' . base_url() . 'logout" class="menu-link">
                <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="menu-text">Cerrar Sesión</span>
            </a>
          </li>';
    
    echo '</ul>
        </nav>';
}
// Función para sanear nombres de archivo
function sanitize_filename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
    $filename = preg_replace('/_+/', '_', $filename);
    return trim($filename, '_');
}

// Función para verificar si es una petición AJAX
function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Función para redireccionar
function redirect($url, $statusCode = 303) {
    header('Location: ' . $url, true, $statusCode);
    exit();
}