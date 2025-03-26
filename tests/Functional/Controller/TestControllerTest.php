<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Common\FunctionalTestCase;
use App\Tests\Common\Request;
use App\Tests\Common\Response;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class TestControllerTest extends FunctionalTestCase
{
    /**
     * @param array<string, string|int|null> $dtoData
     */
    #[DataProvider('getValidDtoData')]
    public function testEndpointReturnsResponseForValidInput($dtoData): void
    {
        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/test/text/123',
            $dtoData
        );

        $json = $this->decodeJsonFromResponse($response);

        self::assertArrayHasKey('method', $json);
        self::assertSame('POST', $json['method']);
        self::assertArrayHasKey('parameter_1', $json);
        self::assertSame('text', $json['parameter_1']);
        self::assertArrayHasKey('parameter_2', $json);
        self::assertSame('123', $json['parameter_2']);

        self::assertArrayHasKey('request_dto', $json);
        self::assertArrayHasKey('body_parameter_1', $json['request_dto']);
        self::assertSame($dtoData['body_parameter_1'], $json['request_dto']['body_parameter_1']);
        self::assertArrayHasKey('body_parameter_2', $json['request_dto']);
        self::assertSame($dtoData['body_parameter_2'], $json['request_dto']['body_parameter_2']);
    }

    /**
     * @return array<mixed>
     */
    public static function getValidDtoData(): array
    {
        return [
            [[
                'body_parameter_1' => 'more_text',
                'body_parameter_2' => 123,
            ]],
            [[
                'body_parameter_1' => 'more_text',
                'body_parameter_2' => null,
            ]],
        ];
    }

    /**
     * @param array<string, string|int|null> $dtoData
     */
    #[DataProvider('getInvalidDtoData')]
    public function testEndpointReturnsBadRequestForInvalidInput($dtoData): void
    {
        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/test/text/123',
            $dtoData
        );

        $json = $this->decodeJsonFromResponse($response, Response::HTTP_BAD_REQUEST);
        self::assertArrayHasKey('error', $json);
        self::assertIsString($json['error']);
        self::assertStringStartsWith('Bad Request: ', $json['error']);
    }

    /**
     * @return array<mixed>
     */
    public static function getInvalidDtoData(): array
    {
        return [
            [[
                'body_parameter_1' => 'more_text',
            ]],
            [[
                'body_parameter_1' => 'more_text',
                'body_parameter_2' => 'more text',
            ]],
            [[
                'body_parameter_1' => 456,
                'body_parameter_2' => 123,
            ]],
            [[
                'body_parameter_1' => null,
                'body_parameter_2' => 123,
            ]],
        ];
    }
}
