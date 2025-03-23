<?php
declare(strict_types=1);

namespace App\System;

use App\Exception\MethodNotAllowedException;
use App\Exception\NotFoundException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

final class Router
{
    private const ROUTES_FILE = __DIR__ . '/../../config/routes.yaml';

    private string $path;
    private string $method;

    public function __construct(
        private string $uri,
    ) {
        $this->setPath();
        $this->setMethod();
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
        // Get all routes matching by path
        $routes = $this->getRoutesMatchingByPath();
        if (\count($routes) === 0) {
            throw new NotFoundException('Path not found');
        }

        // Check if any matched route is available for current http method
        $routes = $this->getRoutesMatchingByMethod($routes);
        if (\count($routes) === 0) {
            throw new MethodNotAllowedException('Method not allowed');
        }
        if (\count($routes) > 1) {
            throw new RuntimeException('Config error, multiple routes found for the same method');
        }

        $endpoint = array_pop($routes);
        $controller = explode('::', $endpoint['controller']);
        if (\count($controller) !== 2) {
            throw new RuntimeException('Invalid controller definition');
        }

        return [
            $controller[0],
            $controller[1],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function getRoutesMatchingByPath(): array
    {
        /** @var array<string, array<string, string>> $routes */
        $routes = Yaml::parseFile(self::ROUTES_FILE);

        $matchingRoutes = [];
        foreach ($routes as $route => $endpoint) {
            if ($endpoint['path'] === $this->path) {
                $matchingRoutes[$route] = $endpoint;
            }
        }

        return $matchingRoutes;
    }

    /**
     * @param array<string, array<string, string>> $routesMatchingByPath
     *
     * @return array<string, array<string, string>>
     */
    private function getRoutesMatchingByMethod(array $routesMatchingByPath): array
    {
        $matchingRoutes = [];
        foreach ($routesMatchingByPath as $route => $endpoint) {
            if ($endpoint['method'] === $this->method) {
                $matchingRoutes[$route] = $endpoint;
            }
        }

        return $matchingRoutes;
    }

    private function setPath(): void
    {
        $this->path = explode('?', $this->uri)[0];
    }

    private function setMethod(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $this->method = \is_string($method) ? $method : '';
    }
}
