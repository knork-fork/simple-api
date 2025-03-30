<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\Auth\TokenIssueRequest;
use App\Entity\Token;
use App\Response\JsonResponse;
use App\Service\TokenIssueService;
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
        /** @var int $user_id */
        $user_id = Auth::getAuthenticatedUserId();

        $tokens = (new Token())->getArrayBy('user_id', $user_id);

        return new JsonResponse($tokens);
    }
}
