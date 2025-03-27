<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Common\FunctionalTestCase;
use App\Tests\Common\Request;
use App\Tests\Common\Response;

/**
 * @internal
 */
final class AuthControllerTest extends FunctionalTestCase
{
    public const TEST_USER = 'test_user';

    public function testIssueTokenReturnsResponseForValidInput(): void
    {
        $data = [
            'username' => self::TEST_USER,
            'secret' => 'password',
            'description' => 'token description',
            'expires' => '2026-03-26T00:00:00Z',
            'is_read_only' => false,
        ];
        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/auth/token',
            $data
        );

        $json = $this->decodeJsonFromResponse($response, Response::HTTP_CREATED);
        self::assertArrayHasKey('token_id', $json);
        self::assertArrayHasKey('token', $json);
        self::assertArrayHasKey('description', $json);
        self::assertArrayHasKey('expires', $json);
    }
}
