<?php

use App\Controllers\ProductoController;

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';
    $len = strlen($prefix);
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require_once '../vendor/autoload.php';
require_once 'config/database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$db = Database::getInstance()->getConnection();
$controller = new ProductoController($db);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$key = array_search('productos', $uri);

if ($key === false) {
    http_response_code(404);
    echo json_encode(["mensaje" => "Ruta no encontrada"]);
    exit();
}

$id = isset($uri[$key + 1]) ? $uri[$key + 1] : null;
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if ($id) {
            $controller->getById($id);
        } else {
            $controller->getAll();
        }

        break;
    case 'POST':
        $controller->create();
        
        break;
    case 'PUT':
        if ($id) {
            $controller->update($id);
        }

        break;
    case 'DELETE':
        if ($id) {
            $controller->delete($id);
        }
        
        break;
    default:
        http_response_code(405);
        break;
}