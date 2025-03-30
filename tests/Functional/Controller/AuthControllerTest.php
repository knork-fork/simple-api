<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Common\FunctionalTestCase;
use App\Tests\Common\Request;
use App\Tests\Common\Response;
use App\Tests\Fixtures\TokenFixture;
use App\Tests\Fixtures\UserFixture;

/**
 * @internal
 */
final class AuthControllerTest extends FunctionalTestCase
{
    public const TEST_USER_PREFIX = 'test_user_';

    /* route: auth_token */

    public function testIssueTokenReturnsTokenForUniqueUsername(): void
    {
        $uniqueTestUser = uniqid(self::TEST_USER_PREFIX, true);
        $this->callIssueTokenWithValidInput($uniqueTestUser);
    }

    public function testIssueTokenReturnsTokenForSameUsernameAndCorrectSecret(): void
    {
        // TEST_USERNAME is not unique - it was already used in the UserFixture
        $this->callIssueTokenWithValidInput(UserFixture::TEST_USERNAME);
    }

    private function callIssueTokenWithValidInput(string $username): void
    {
        $data = [
            'username' => $username,
            'secret' => UserFixture::TEST_USER_SECRET,
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

    public function testIssueTokenReturnsErrorForSameUserAndInvalidSecret(): void
    {
        $data = [
            'username' => UserFixture::TEST_USERNAME, // username already exists, but used with different secret
            'secret' => 'incorrect_password',
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

    /* route: auth_list */

    public function testListTokensWithNoAuthReturnsUnauthorized(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/auth/token'
        );

        $json = $this->decodeJsonFromResponse($response, Response::HTTP_UNAUTHORIZED);
        self::assertArrayHasKey('error', $json);
        self::assertIsString($json['error']);
        self::assertSame('Missing or invalid auth header', $json['error']);
    }

    public function testListTokensReturnsResponse(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/auth/token',
            [],
            [
                'Access-Token: ' . TokenFixture::TEST_TOKEN,
            ]
        );

        $json = $this->decodeJsonFromResponse($response, Response::HTTP_OK);
        self::assertTrue(\count($json) > 0);

        $token = $json[0];
        self::assertIsArray($token);
        self::assertArrayHasKey('user_id', $token);
        self::assertArrayHasKey('token_hash', $token);
        self::assertArrayHasKey('token_id', $token);
        self::assertArrayHasKey('token_lookup', $token);
        self::assertArrayHasKey('description', $token);
        self::assertArrayHasKey('created_at', $token);
        self::assertArrayHasKey('expires_at', $token);
        // self::assertArrayHasKey('scope', $token);
    }
}
