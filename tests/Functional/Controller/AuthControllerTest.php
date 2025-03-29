<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Common\FunctionalTestCase;
use App\Tests\Common\Request;
use App\Tests\Common\Response;
use App\Tests\Fixtures\UserFixture;

/**
 * @internal
 */
final class AuthControllerTest extends FunctionalTestCase
{
    public const TEST_USER_PREFIX = 'test_user_';

    public function testIssueTokenReturnsResponseForValidInput(): void
    {
        $uniqueTestUser = uniqid(self::TEST_USER_PREFIX, true);

        $data = [
            'username' => $uniqueTestUser,
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

    public function testIssueTokenReturnsErrorForDuplicateUser(): void
    {
        $data = [
            'username' => UserFixture::TEST_USERNAME, // not unique
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

        $json = $this->decodeJsonFromResponse($response, Response::HTTP_BAD_REQUEST);
        self::assertArrayHasKey('error', $json);
        self::assertIsString($json['error']);
        self::assertSame('Failed to save user', $json['error']);
    }
}
