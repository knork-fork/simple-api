<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\Auth\TokenIssueRequest;
use App\Dto\Auth\TokenIssueResponse;
use App\Response\JsonResponse;

final class AuthController
{
    public static function issueToken(TokenIssueRequest $tokenIssueRequest): JsonResponse
    {
        // to-do: implement token generation logic

        $tokenResponse = new TokenIssueResponse(
            'token_id',
            'token',
            $tokenIssueRequest->description,
            $tokenIssueRequest->expires
        );

        return new JsonResponse($tokenResponse->toArray());
    }
}
