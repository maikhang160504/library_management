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

// Book Routes
$router->get('/', function () {
    callControllerMethod('App\Controllers\BookController@index');
});

// Route cho xem chi tiết sách
$router->get('/books/(\d+)', function ($id) {
    callControllerMethod('App\Controllers\BookController@show', [$id]);
});

// Route cho quản lý mượn sách
$router->get('/borrows', function () {
    callControllerMethod('App\Controllers\BorrowController@index');
});

$router->get('/borrows/create', function () {
    callControllerMethod('App\Controllers\BorrowController@create');
});

$router->get('/borrows/detail/(\d+)', function ($id) {
    callControllerMethod('App\Controllers\BorrowController@show', [$id]);
});
$router->post('/borrows/store', function () {
    callControllerMethod('App\Controllers\BorrowController@store');
});

// Route cho quản lý trả sách
$router->get('/returns', function () {
    callControllerMethod('App\Controllers\ReturnController@index');
});

$router->get('/returns/return', function () {
    callControllerMethod('App\Controllers\ReturnController@return');
});
$router->get('/returns/detail/(\d+)', function ($id) {
    callControllerMethod('App\Controllers\ReturnController@show', [$id]);
});
$router->get('/reports', function () {
    callControllerMethod('App\Controllers\ReportController@index');
});
$router->get('/reports/monthly-borrow-stats', function () {
    callControllerMethod('App\Controllers\ReportController@monthlyBorrowStats');
});

$router->get('/reports/yearly-reader-stats', function () {
    callControllerMethod('App\Controllers\ReportController@yearlyReaderStats');
});
$router->get('/reports/top-readers-most-borrowed-book', function () {
        callControllerMethod('App\Controllers\ReportController@topReaders_mostBorrowedBook');
});
$router->get('/reports/borrow-return-report', function () {
    callControllerMethod('App\Controllers\ReportController@borrowReturnReport');
});
$router->run();