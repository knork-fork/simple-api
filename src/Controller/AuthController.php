<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\Auth\TokenIssueRequest;
use App\Dto\Auth\TokenIssueResponse;
use App\Entity\Token;
use App\Entity\User;
use App\Exception\BadRequestException;
use App\Response\JsonResponse;
use App\System\Auth;
use RuntimeException;

final class AuthController
{
    public static function issueToken(TokenIssueRequest $tokenIssueRequest): JsonResponse
    {
        $user = (new User())->hydrate([
            'username' => $tokenIssueRequest->username,
            'secret_hash' => password_hash($tokenIssueRequest->secret, \PASSWORD_BCRYPT),
        ]);
        try {
            $user->save();
        } catch (RuntimeException $e) {
            throw new BadRequestException('Failed to save user');
        }
        if ($user->id === null) {
            throw new RuntimeException('Failed to save user');
        }

        $token = Token::getToken();
        $token->user_id = $user->id;
        $token->description = $tokenIssueRequest->description;
        $token->expires_at = $tokenIssueRequest->expires;
        $token->save();
        if ($token->id === null || $token->getUnhashedToken() === null) {
            throw new RuntimeException('Failed to save token');
        }

        $tokenResponse = new TokenIssueResponse(
            $token->token_id,
            $token->getUnhashedToken(),
            $tokenIssueRequest->description,
            $tokenIssueRequest->expires
        );

        return new JsonResponse($tokenResponse->toArray());
    }

    public static function listTokens(): JsonResponse
    {
        /** @var int $user_id */
        $user_id = Auth::getAuthenticatedUserId();

        $tokens = (new Token())->getArrayBy('user_id', $user_id);

        return new JsonResponse($tokens);
    }
}
