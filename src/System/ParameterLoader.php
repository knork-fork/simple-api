<?php
declare(strict_types=1);

namespace App\System;

final class ParameterLoader
{
    /**
     * @return string[]
     */
    public static function getUriParameters(string $path, string $uri): array
    {
        $pathParts = explode('/', ltrim($path, '/'));
        $uriParts = explode('/', ltrim($uri, '/'));

        $parameters = [];
        foreach ($pathParts as $index => $pathPart) {
            if ($pathPart === $uriParts[$index]) {
                continue;
            }

            if (str_starts_with($pathPart, '{') && str_ends_with($pathPart, '}')) {
                $parameters[] = $uriParts[$index];
            }
        }

        return $parameters;
    }
}
