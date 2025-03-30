<?php
declare(strict_types=1);

namespace App\System;

use App\Entity\Token;
use App\Exception\UnauthorizedException;
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

        if (!password_verify($token, $tokenObject->token_hash)) {
            throw new UnauthorizedException('Missing or invalid auth header');
        }

        self::$userId = $tokenObject->user_id;

        return $tokenObject->user_id;
    }
}
