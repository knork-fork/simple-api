<?php
declare(strict_types=1);

use App\Exception\BadRequestException;
use App\Exception\NotFoundException;
use App\Response\ExceptionResponse;
use App\System\Router;
use KnorkFork\LoadEnvironment\Environment;
use Symfony\Component\Yaml\Yaml;

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Build route cache
$yamlPath = '/application/config/routes.yaml';
$cachePath = '/application/config/routes.cache.php';
if (!file_exists($cachePath) || filemtime($yamlPath) > filemtime($cachePath)) {
    $parsed = Yaml::parseFile($yamlPath);
    file_put_contents($cachePath, '<?php return ' . var_export($parsed, true) . ';');
}

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
    $suppressThrow = $e instanceof NotFoundException || $e instanceof BadRequestException;

    $exception = new ExceptionResponse($e, $suppressThrow);
    $exception->output();

    if (!$exception->suppressThrow) {
        throw $e;
    }
}

exit;
