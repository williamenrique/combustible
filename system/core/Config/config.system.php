<?php
const BASE_URL = "http://192.168.0.240/combustible/";
// const BASE_URL = "https://estacion.infinityfree.me/";
const HEAD = "src/include/head.php";
const FOOTER = "src/include/footer.php";
// const BACK_URL = $_SERVER['REQUEST_URI'];
const LIBS = "system/core/Libraries/";
const VIEWS = "system/app/Views/";
const titulo = "SISTEM ";
date_default_timezone_set('America/Caracas');

//rutas de assets
const CSS = BASE_URL."src/css/";
const JS = BASE_URL."src/js/";
const IMG = BASE_URL."src/img/";
//constantes del template admin
const CSS_VENDORS = BASE_URL."src/vendors/css/";
const JS_VENDORS = BASE_URL."src/vendors/js/";
const IMG_VENDORS = BASE_URL."src/vendors/img/";
const VENDORS = BASE_URL."src/vendors/";
const PLUGINS = BASE_URL."src/plugins/";

const CONTROLLER = BASE_URL."system/core/Libraries/Controllers.php";
const LOAD = BASE_URL."system/core/Libraries/Load.php";

//constantes de base de datos local
const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "combustible_db";
// base de datos en la nube
// const DB_HOST = "sql202.infinityfree.com";
// const DB_USER = "if0_39962441";
// const DB_PASS = "25estacion25";
// const DB_NAME = "if0_39962441_estacion";
//const DB_NAME = "if0_37518912_prueba";
const DB_CHARSET = "charset=utf8";
//delimitador decimal y millar Ej. 24,1999.00
const SPD = ',';
const SPM = '.';
//simbolo de moneda
const SMONEY = '$';
//constantes de encriptacion
define('METHOD','AES-256-CBC');
define('SECRET_KEY','$busYaracuy');
define('SECRET_IV','101712');
define('ESTACION','E/S TACHIRA');
