<?php
use Bramus\Router\Router;

$router = new Router();

/**
 * Hàm hỗ trợ để gọi controller và method từ chuỗi "Controller@method"
 */
function callControllerMethod($controllerMethod, $params = [])
{
    list($controllerClass, $method) = explode('@', $controllerMethod);

    if (!class_exists($controllerClass)) {
        throw new Exception("Controller class {$controllerClass} not found.");
    }

    if (!method_exists($controllerClass, $method)) {
        throw new Exception("Method {$method} not found in controller {$controllerClass}.");
    }

    $controller = new $controllerClass();
    call_user_func_array([$controller, $method], $params);
}

// Auth Routes
$router->get('/login', function() {
    callControllerMethod('App\Controllers\AuthController@login');
});
$router->post('/login', function() {
    callControllerMethod('App\Controllers\AuthController@login');
});
$router->get('/logout', function() {
    callControllerMethod('App\Controllers\AuthController@logout');
});

// Book Routes
$router->get('/', function() {
    callControllerMethod('App\Controllers\BookController@index');
});
$router->get('/books/(\d+)', function($id) {
    callControllerMethod('App\Controllers\BookController@show', [$id]);
});

$router->run();