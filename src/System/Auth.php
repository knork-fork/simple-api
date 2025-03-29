<?php
declare(strict_types=1);

namespace App\System;

use App\Entity\Token;

final class Auth
{
    private static ?int $userId = null;

    public static function getAuthenticatedUserId(): ?int
    {
        if (self::$userId !== null) {
            return self::$userId;
        }

        $headers = getallheaders();
        if (!isset($headers['Access-Token'])) {
            return null;
        }

        $token = $headers['Access-Token'];

        /** @var Token $tokenObject */
        $tokenObject = (new Token())->getBy('token_lookup', substr($token, -12));

        if (!password_verify($token, $tokenObject->token_hash)) {
            return null;
        }

        self::$userId = $tokenObject->user_id;

        return $tokenObject->user_id;
    }
}
