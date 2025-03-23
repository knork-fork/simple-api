<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Tests\Common\FunctionalTestCase;
use App\Tests\Common\Request;
use App\Tests\Common\Response;

/**
 * @internal
 */
final class StatusControllerTest extends FunctionalTestCase
{
    public function testStatusEndpointReturnsResponse(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/status'
        );

        $json = $this->decodeJsonFromSuccessfulResponse($response);
        self::assertArrayHasKey('status', $json);
        self::assertSame('ok', $json['status']);
    }

    public function testEndpointReturnsErrorForInvalidMethod(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/status'
        );

        self::assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());

        $json = $this->decodeJsonFromSuccessfulResponse($response, Response::HTTP_METHOD_NOT_ALLOWED);
        self::assertArrayHasKey('error', $json);
        self::assertSame('Method not allowed', $json['error']);
    }

    public function testInvalidRouteFoundReturns404(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/no-route'
        );

        $json = $this->decodeJsonFromSuccessfulResponse($response, Response::HTTP_NOT_FOUND);
        self::assertArrayHasKey('error', $json);
        self::assertSame('Path not found', $json['error']);
    }
}
