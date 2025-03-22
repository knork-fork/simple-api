<?php
declare(strict_types=1);

namespace App\System;

use App\Exception\NotFoundException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

final class Router
{
    private const ROUTES_FILE = __DIR__ . '/../../config/routes.yaml';

    private string $path;

    public function __construct(
        private string $uri,
    ) {
        $this->path = $this->getPath();
    }

    public function callEndpoint(): void
    {
        [$class, $method] = $this->resolveEndpoint();

        $controller = new $class();
        $response = $controller->{$method}();

        $response->output();
    }

    /**
     * @return string[]
     */
    private function resolveEndpoint(): array
    {
        /** @var array<string, array<string, string>> $routes */
        $routes = Yaml::parseFile(self::ROUTES_FILE);

        $controller = null;
        foreach ($routes as $route => $endpoint) {
            if ($endpoint['path'] === $this->path) {
                $controller = $endpoint['controller'];
                break;
            }
        }

        if ($controller === null) {
            throw new NotFoundException('Path not found');
        }

        $controller = explode('::', $controller);
        if (\count($controller) !== 2) {
            throw new RuntimeException('Invalid controller definition');
        }

        return [
            $controller[0],
            $controller[1],
        ];
    }

    private function getPath(): string
    {
        return explode('?', $this->uri)[0];
    }
}
