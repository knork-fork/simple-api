<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\TestRequestDto;
use App\Response\JsonResponse;

final class TestController
{
    public static function test(TestRequestDto $testRequestDto, string $parameter_1, string $parameter_2): JsonResponse
    {
        return new JsonResponse([
            'method' => $_SERVER['REQUEST_METHOD'],
            'parameter_1' => $parameter_1,
            'parameter_2' => $parameter_2,
            'request_dto' => [
                'body_parameter_1' => $testRequestDto->body_parameter_1,
                'body_parameter_2' => $testRequestDto->body_parameter_2,
                'optional_string_parameter' => $testRequestDto->optional_string_parameter,
            ],
        ]);
    }
}
