<?php
use Bramus\Router\Router;

$router = new Router();

/**
 * Hàm hỗ trợ gọi controller và method từ chuỗi "Controller@method"
 */
function callControllerMethod($controllerMethod, $params = [])
{
    list($controllerClass, $method) = explode('@', $controllerMethod);

    // Thêm namespace nếu chưa có
    if (strpos($controllerClass, 'App\\Controllers\\') !== 0) {
        $controllerClass = 'App\\Controllers\\' . $controllerClass;
    }

    if (!class_exists($controllerClass)) {
        throw new Exception("Controller class {$controllerClass} not found.");
    }

    if (!method_exists($controllerClass, $method)) {
        throw new Exception("Method {$method} not found in controller {$controllerClass}.");
    }

    $controller = new $controllerClass();
    call_user_func_array([$controller, $method], $params);
}

/**
 * ---------------------------
 * BOOK ROUTES
 * ---------------------------
 */
$router->get('/', function () {
    callControllerMethod('BookController@index');
});
$router->get('/books/add', function () {
    callControllerMethod('BookController@add');
});
$router->post('/books/store', function () {
    callControllerMethod('BookController@store');
});
$router->get('/books/{id}', function ($id) {
    callControllerMethod('BookController@show', [$id]);
});
$router->post('/books/update', function () {
    callControllerMethod('BookController@update');
});
$router->get('/books/filter', function () {
    callControllerMethod('BookController@filter');
});
$router->match('GET|POST', '/books', function () {
    callControllerMethod('BookController@index');
});
$router->get('/books/search', function () {
    callControllerMethod('BookController@search');
});
/**
 * ---------------------------
 * BORROW ROUTES
 * ---------------------------
 */
$router->get('/borrows', function () {
    callControllerMethod('BorrowController@index');
});
$router->get('/borrows/create', function () {
    callControllerMethod('BorrowController@create');
});
$router->post('/borrows/store', function () {
    callControllerMethod('BorrowController@store');
});
$router->get('/borrows/detail/(\d+)', function ($id) {
    callControllerMethod('BorrowController@show', [$id]);
});

/**
 * ---------------------------
 * RETURN ROUTES
 * ---------------------------
 */
$router->get('/returns', function () {
    callControllerMethod('ReturnController@index');
});
$router->get('/returns/return', function () {
    callControllerMethod('ReturnController@return');
});
$router->get('/returns/detail', function () {
    callControllerMethod('App\Controllers\ReturnController@show');
});

/**
 * ---------------------------
 * REPORT ROUTES
 * ---------------------------
 */
$router->get('/reports', function () {
    callControllerMethod('ReportController@index');
});
$router->get('/reports/borrow-stats', function () {
    callControllerMethod('App\Controllers\ReportController@BorrowStats');
});
$router->get('/reports/yearly-reader-stats', function () {
    callControllerMethod('ReportController@yearlyReaderStats');
});
$router->get('/reports/top-readers-most-borrowed-book', function () {
    callControllerMethod('ReportController@topReaders_mostBorrowedBook');
});
$router->get('/reports/borrow-return-report', function () {
    callControllerMethod('ReportController@borrowReturnReport');
});
$router->get('/reports/penalties', function () {
    callControllerMethod('ReportController@penaltyReport'); 
});
$router->get('/reports/upcoming-returns', function () {
    callControllerMethod('ReportController@upcomingReturns');
});

$router->get('/reports/penalties', function () {
    callControllerMethod('ReportController@penaltiesStats'); 
});


/**
 * ---------------------------
 * READER ROUTES
 * ---------------------------
 */
$router->get('/readers', function () {
    callControllerMethod('ReaderController@index');
});
$router->get('/readers/create', function () {
    callControllerMethod('ReaderController@create');
});
$router->post('/readers/store', function () {
    callControllerMethod('ReaderController@store');
});
$router->get('/readers/(\d+)/edit', function ($id) {
    callControllerMethod('ReaderController@edit', [$id]);
});
$router->post('/readers/(\d+)/update', function ($id) {
    callControllerMethod('ReaderController@update', [$id]);
});
$router->get('/readers/(\d+)/delete', function ($id) {
    callControllerMethod('ReaderController@delete', [$id]);
});

$router->get('/readers/(\d+)/detail', function ($id) {
    callControllerMethod('ReaderController@detail', [$id]);
});

$router->get('/readers/search', function () {
    callControllerMethod('ReaderController@search');
});


/**
 * ---------------------------
 * PENALTY ROUTES
 * ---------------------------
 */
$router->get('/penalties', function () {
    callControllerMethod('PenaltyController@index');
});
$router->get('/penalties/create', function () {
    callControllerMethod('PenaltyController@create');
});
$router->post('/penalties/store', function () {
    callControllerMethod('PenaltyController@store');
});
$router->get('/penalties/(\d+)/edit', function ($id) {
    callControllerMethod('PenaltyController@edit', [$id]);
});
$router->post('/penalties/(\d+)/update', function ($id) {
    callControllerMethod('PenaltyController@update', [$id]);
});
$router->get('/penalties/(\d+)/delete', function ($id) {
    callControllerMethod('PenaltyController@delete', [$id]);
});

$router->get('/penalties/(\d+)/detail', function ($id) {
    callControllerMethod('PenaltyController@detail', [$id]);
});

$router->get('/penalty/sendEmail/(\d+)', function ($id) {
    callControllerMethod('PenaltyController@sendReminderEmail', [$id]);
});

$router->get('/penalty/search', function () {
    callControllerMethod('PenaltyController@search');
});


/**
 * ---------------------------
 * CHẠY ROUTER
 * ---------------------------
 */
$router->run();
