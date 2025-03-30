<?php
declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\User;
use RuntimeException;

final class UserFixture extends Fixture
{
    public const TEST_USERNAME = 'test_user';
    public const TEST_USER_SECRET = 'test123';

    public function load(): void
    {
        $this->createTestUser();
    }

    private function createTestUser(): void
    {
        $user = (new User())->hydrate([
            'username' => self::TEST_USERNAME,
            'secret_hash' => $this->hashSecret(self::TEST_USER_SECRET),
        ]);
        $user->save();

        if ($user->id === null) {
            throw new RuntimeException('Failed to save test user');
        }
    }

    private function hashSecret(string $secret): string
    {
        return password_hash($secret, \PASSWORD_BCRYPT);
    }

    public static function getTestUser(): User
    {
        $user = (new User())->getBy('username', self::TEST_USERNAME);
        if ($user->id === null) {
            throw new RuntimeException('Test user not found');
        }

        return $user;
    }
}
