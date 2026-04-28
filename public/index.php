<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/constants.php';
require_once __DIR__ . '/../app/config/roles.php';
require_once __DIR__ . '/../app/config/database.php';

use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;

Session::start();

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'DashboardController';
$method = $url[1] ?? 'index';
$params = array_slice($url, 2);

$controllerMap = [
    '' => 'DashboardController',
    'login' => 'AuthController',
    'auth' => 'AuthController',
    'logout' => 'AuthController',
    'dashboard' => 'DashboardController',
    'paciente' => 'PacienteController',
    'consulta' => 'ConsultaController',
    'receta' => 'RecetaController',
    'farmacia' => 'FarmaciaController',
    'dispensacion' => 'DispensacionController',
    'trazabilidad' => 'TrazabilidadController',
    'reportes' => 'ReporteController',
    'usuarios' => 'UsuarioController',
];

$controllerClass = $controllerMap[$url[0] ?? ''] ?? $controllerName;
$controllerClass = "App\\Controllers\\$controllerClass";

if (!class_exists($controllerClass)) {
    http_response_code(404);
    include __DIR__ . '/../app/views/errors/404.php';
    exit;
}

$controller = new $controllerClass();

if (!method_exists($controller, $method)) {
    http_response_code(404);
    include __DIR__ . '/../app/views/errors/404.php';
    exit;
}

call_user_func_array([$controller, $method], $params);