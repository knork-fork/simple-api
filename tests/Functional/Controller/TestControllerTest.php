<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Common\FunctionalTestCase;
use App\Tests\Common\Request;

/**
 * @internal
 */
final class TestControllerTest extends FunctionalTestCase
{
    public function testEndpointReturnsResponseForInput(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/test/blabla/123'
        );

        $json = $this->decodeJsonFromSuccessfulResponse($response);

        self::assertArrayHasKey('method', $json);
        self::assertSame('POST', $json['method']);
        self::assertArrayHasKey('parameter_1', $json);
        self::assertSame('blabla', $json['parameter_1']);
        self::assertArrayHasKey('parameter_2', $json);
        self::assertSame('123', $json['parameter_2']);

        // static::assertArrayHasKey('response_dto', $json);
        // static::assertNull($json['response_dto']);
    }
}
