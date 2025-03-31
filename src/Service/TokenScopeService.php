<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Token;
use App\System\Auth;

final class TokenScopeService
{
    public static function canTokenEditTokens(Token $token): bool
    {
        if (!self::isTokenFromAuthorizedUser($token)) {
            return false;
        }

        // To-do: Check if the token has the 'edit' scope for tokens
        // Auth class should somehow also save the token used for authentication
        // e.g. return in_array('edit', $token->getScopes(), true);

        return true;
    }

    private static function isTokenFromAuthorizedUser(Token $token): bool
    {
        return $token->user_id === Auth::getAuthenticatedUserId();
    }
}
