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
    $exception = new ExceptionResponse($e);
    $exception->output();

    if (!$exception->suppressThrow && !$e instanceof NotFoundException) {
        throw $e;
    }
}

exit;
