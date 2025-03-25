<?php
declare(strict_types=1);

namespace App\Controller;

use App\Response\JsonResponse;

final class TestController
{
    public static function test(string $parameter_1, string $parameter_2): JsonResponse
    {
        return new JsonResponse([
            'method' => $_SERVER['REQUEST_METHOD'],
            'parameter_1' => $parameter_1,
            'parameter_2' => $parameter_2,
            'response_dto' => null,
        ]);
    }
}
