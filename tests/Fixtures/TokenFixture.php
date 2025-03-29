<?php
declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\Token;
use RuntimeException;

final class TokenFixture extends Fixture
{
    public const TEST_TOKEN = 'd173c1f891bbba3579c6bd91307ac959f8ec8c0cf2b4dee88be837bcdf9c930d';
    public const TEST_TOKEN_ID = 'deae0e137f1b871f';
    public const TOKEN_LOOKUP = '37bcdf9c930d';

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }

    public function load(): void
    {
        $this->createTestToken();
    }

    private function createTestToken(): void
    {
        $user = UserFixture::getTestUser();

        $token = (new Token())->hydrate([
            'user_id' => $user->id,
            'token_hash' => password_hash(self::TEST_TOKEN, \PASSWORD_BCRYPT),
            'token_id' => self::TEST_TOKEN_ID,
            'token_lookup' => self::TOKEN_LOOKUP,
            'description' => 'test token',
            'expires_at' => null,
        ]);
        $token->save();

        if ($token->id === null) {
            throw new RuntimeException('Failed to save test token');
        }
    }
}
