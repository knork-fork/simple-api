<?php
declare(strict_types=1);

use App\Exception\NotFoundException;
use App\Response\ExceptionResponse;
use App\System\Router;
use KnorkFork\LoadEnvironment\Environment;

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
Environment::load(__DIR__ . '/../.env');
$overrideEnvToTest = false;
if (getenv('ALLOW_ENV_OVERRIDE') === 'true') {
    if (isset($_SERVER['HTTP_X_APP_ENV']) && $_SERVER['HTTP_X_APP_ENV'] === 'test') {
        $overrideEnvToTest = true;
    }
}
if ($overrideEnvToTest) {
    Environment::load(__DIR__ . '/../.env', ['test']);
} else {
    Environment::load(__DIR__ . '/../.env');
}

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
