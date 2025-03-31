<?php
declare(strict_types=1);

namespace App\System;

use App\Entity\Token;
use App\Exception\UnauthorizedException;
use App\System\Enum\HashMethod;
use KnorkFork\LoadEnvironment\Environment;
use Throwable;

final class Auth
{
    private static ?int $userId = null;

    /**
     * @throws UnauthorizedException
     */
    public static function getAuthenticatedUserId(): int
    {
        if (self::$userId !== null) {
            return self::$userId;
        }

        $headers = getallheaders();
        if (!isset($headers['Access-Token'])) {
            throw new UnauthorizedException('Missing or invalid auth header');
        }

        $token = $headers['Access-Token'];
        try {
            $tokenObject = (new Token())->getBy('token_lookup', substr($token, -12));
        } catch (Throwable) {
            throw new UnauthorizedException('Missing or invalid auth header');
        }

        if (!self::verifyHash($token, $tokenObject->token_hash, HashMethod::HMAC)) {
            throw new UnauthorizedException('Missing or invalid auth header');
        }

        self::$userId = $tokenObject->user_id;

        return $tokenObject->user_id;
    }

    public static function getHash(string $input, HashMethod $method): string
    {
        return match ($method) {
            HashMethod::Bcrypt => password_hash($input, \PASSWORD_BCRYPT),
            HashMethod::HMAC => hash_hmac('sha256', $input, Environment::getStringEnv('HMAC_SECRET')),
        };
    }

    public static function verifyHash(string $input, string $hash, HashMethod $method): bool
    {
        return match ($method) {
            HashMethod::Bcrypt => password_verify($input, $hash),
            HashMethod::HMAC => hash_equals(hash_hmac('sha256', $input, Environment::getStringEnv('HMAC_SECRET')), $hash),
        };
    }
}
