<?php
declare(strict_types=1);

use App\Exception\NotFoundException;
use App\Response\ExceptionResponse;
use App\System\Router;

require __DIR__ . '/../vendor/autoload.php';

$uri = $_SERVER['REQUEST_URI'];
$uri = is_string($uri) ? $uri : '';

$router = new Router($uri);
try {
    $router->callEndpoint();
} catch (Throwable $e) {
    // Suppress user-caused exceptions being thrown and logged
    $suppressThrow = $e instanceof NotFoundException;

    $exception = new ExceptionResponse($e, $suppressThrow);
    $exception->output();

    if (!$exception->suppressThrow) {
        throw $e;
    }
}

exit;
