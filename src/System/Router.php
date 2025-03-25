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
    private string $method;

    public function __construct(
        private string $uri,
    ) {
        $this->setPath();
        $this->setMethod();
    }

    public function callEndpoint(): void
    {
        [$class, $method, $args] = $this->resolveEndpoint();

        $controller = new $class();
        if (\count($args) === 0) {
            $response = $controller->{$method}();
        } else {
            $response = $controller->{$method}(...$args);
        }

        $response->output();
    }

    /**
     * @return array{0: string, 1: string, 2: mixed[]}
     */
    private function resolveEndpoint(): array
    {
        $route = $this->getMatchingRoute();
        $controller = explode('::', $route['controller']);
        if (\count($controller) !== 2) {
            throw new RuntimeException('Invalid controller definition');
        }

        // to-do: implement DTO parameters
        // $dto = ParameterLoader::getDto($route['dto'], $this->request);

        $uriParameters = ParameterLoader::getUriParameters($route['path'], $this->path);

        /*if ($dto !== null) {
            // DTO will always be passed as the first argument, followed by URI parameters
            $args = array_merge([$dto], $uriParameters);
        } else {
            $args = $uriParameters;
        }*/

        $args = $uriParameters;

        return [
            $controller[0],
            $controller[1],
            $args,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getMatchingRoute(): array
    {
        /** @var array<string, array<string, string>> $routes */
        $routes = Yaml::parseFile(self::ROUTES_FILE);

        $matchingRoutes = [];
        foreach ($routes as $route) {
            if ($this->doesRequestMatchConfiguredRoute($route)) {
                $matchingRoutes[] = $route;
            }
        }

        if (\count($matchingRoutes) === 0) {
            throw new NotFoundException('Path not found');
        }
        if (\count($matchingRoutes) > 1) {
            throw new RuntimeException('Config error, multiple routes found for the same method');
        }

        return $matchingRoutes[0];
    }

    /**
     * @param array<string, string> $route
     */
    private function doesRequestMatchConfiguredRoute(array $route): bool
    {
        if ($route['method'] !== $this->method) {
            return false;
        }

        return PathMatcher::doesPathMatch($route['path'], $this->path);
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
