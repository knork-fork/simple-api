<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\Auth\TokenIssueRequest;
use App\Entity\Token;
use App\Exception\ForbiddenException;
use App\Response\JsonResponse;
use App\Response\NoContentResponse;
use App\Service\TokenIssueService;
use App\Service\TokenScopeService;
use App\System\Auth;

final class AuthController
{
    public static function issueToken(TokenIssueRequest $tokenIssueRequest): JsonResponse
    {
        $tokenIssueService = new TokenIssueService();
        $tokenResponse = $tokenIssueService->issueToken($tokenIssueRequest);

        return new JsonResponse($tokenResponse->toArray());
    }

    public static function listTokens(): JsonResponse
    {
        $user_id = Auth::getAuthenticatedUserId();

        $tokens = (new Token())->getArrayBy('user_id', $user_id);

        return new JsonResponse($tokens);
    }

    public static function revokeToken(string $token_id): NoContentResponse
    {
        $token = (new Token())->getBy('token_id', $token_id);

        if (TokenScopeService::canTokenEditTokens($token) === false) {
            throw new ForbiddenException('You are not allowed to revoke this token');
        }

        $token->delete();

        return new NoContentResponse();
    }

    public static function validateToken(): JsonResponse
    {
        // No action necessary, Auth::getAuthenticatedUserId() already called in router

        return new JsonResponse(['valid' => true]);
    }
}
